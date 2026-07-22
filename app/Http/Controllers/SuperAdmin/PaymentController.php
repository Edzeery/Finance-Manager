<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Enums\PaymentStatus;
use App\Events\PaymentCompleted;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Mail\PaymentRefunded;
use App\Services\OnboardingService;
use App\Services\Payments\PaymentGatewayRegistry;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OnboardingService $onboardingService,
        private readonly PaymentGatewayRegistry $registry,
    ) {}

    private function webhookMethods(): array
    {
        return array_keys(array_filter(
            $this->registry->all(),
            fn ($g) => $g->webhook,
        ));
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payments'));

        $query = Payment::withoutWorkspace()->with('workspace', 'subscription.plan', 'user', 'verification', 'paymentMethod', 'refundedBy');

        $status = $request->input('status', 'all');
        if ($status !== 'all') {
            $statusEnum = PaymentStatus::tryFrom($status);
            if ($statusEnum) {
                $query->where('status', $statusEnum->value);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('workspace', fn ($w) => $w->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('refunded')) {
            $request->refunded === 'yes'
                ? $query->whereNotNull('refunded_at')
                : $query->whereNull('refunded_at');
        }

        if ($request->filled('method')) {
            $query->whereHas('paymentMethod', fn ($q) => $q->where('key', $request->method));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $payments = $query->latest('created_at')->paginate($perPage);

        $gatewayKeys = array_keys($this->registry->all());
        $webhookMethods = $this->webhookMethods();

        $paymentsData = $payments->mapWithKeys(fn ($p) => [
            $p->id => [
                'id' => $p->id,
                'reference' => $p->reference,
                'transaction_id' => $p->transaction_id,
                'chargily_checkout_id' => $p->chargily_checkout_id,
                'gateway_reference' => $p->gateway_reference,
                'metadata' => $p->metadata,
                'gateway_payload' => $p->gateway_payload,
                'webhook_payload' => $p->webhook_payload,
            ],
        ]);

        $countAll = Payment::withoutWorkspace()->count();
        $countPending = Payment::withoutWorkspace()->where('status', 'checkout.pending')->count();
        $countPaid = Payment::withoutWorkspace()->where('status', 'checkout.paid')->count();
        $countFailed = Payment::withoutWorkspace()->where('status', 'checkout.failed')->count();
        $countCanceled = Payment::withoutWorkspace()->where('status', 'checkout.canceled')->count();
        $countExpired = Payment::withoutWorkspace()->where('status', 'checkout.expired')->count();

        $methodSubTabs = collect(['' => ['label' => __('general.all')]])
            ->union(collect($gatewayKeys)->mapWithKeys(fn ($m) => [$m => ['label' => __("super-admin.{$m}")]]))
            ->toArray();

        return view('super-admin.payments', $this->withBreadcrumbs(compact(
            'payments', 'gatewayKeys', 'webhookMethods', 'paymentsData',
            'countAll', 'countPending', 'countPaid', 'countFailed',
            'countCanceled', 'countExpired', 'methodSubTabs'
        )));
    }

    public function approve(int $id, Request $request)
    {
        $payment = Payment::withoutWorkspace()->findOrFail($id);
        $methodKey = $payment->paymentMethod?->key;
        if (in_array($methodKey, $this->webhookMethods())) {
            return redirect()->back()->withErrors([
                'error' => __('super-admin.webhook_payment_verify', ['method' => $methodKey]),
            ]);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $this->paymentService->verifyPayment(
            $payment,
            'approved',
            auth()->id(),
            $request->input('notes', 'Approved by admin'),
            $request->input('transaction_reference'),
        );

        app(ActivityLogServiceInterface::class)->log(
            auth()->id(), 'payment_approved', $payment,
            "Payment approved: {$payment->amount} {$payment->currency}",
            ['gateway' => $methodKey, 'workspace_id' => $payment->workspace_id]
        );

        $payment->refresh();

        if ($payment->user_id) {
            $user = User::find($payment->user_id);
            if ($user && $user->pending_plan_id) {
                $this->onboardingService->handlePaymentSuccess($user, $payment);
            }
        }

        $payment->refresh();

        event(new PaymentCompleted($payment));

        return redirect()->back()->with('success', __('super-admin.payment_approved'));
    }

    public function refund(int $id, Request $request)
    {
        $payment = Payment::withoutWorkspace()->findOrFail($id);

        abort_unless($payment->isRefundable(), 422, __('super-admin.payment_not_refundable'));

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:'.$payment->amount,
            'refund_reason' => 'required|string|max:1000',
        ]);

        $payment->update([
            'refunded_at' => now(),
            'refund_amount' => $request->refund_amount,
            'refund_reason' => $request->refund_reason,
            'refunded_by' => auth()->id(),
        ]);

        app(ActivityLogServiceInterface::class)->log(
            auth()->id(), 'payment_refunded', $payment,
            "Payment refunded: {$request->refund_amount} {$payment->currency}",
            ['reason' => $request->refund_reason]
        );

        if ($payment->user && $payment->user->email) {
            try {
                Mail::to($payment->user->email)
                    ->queue(new PaymentRefunded($payment));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->back()->with('success', __('super-admin.payment_refunded'));
    }

    public function reject(int $id, Request $request)
    {
        $payment = Payment::withoutWorkspace()->findOrFail($id);
        $methodKey = $payment->paymentMethod?->key;
        if (in_array($methodKey, $this->webhookMethods())) {
            return redirect()->back()->withErrors([
                'error' => __('super-admin.webhook_payment_verify', ['method' => $methodKey]),
            ]);
        }

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $this->paymentService->verifyPayment(
            $payment,
            'rejected',
            auth()->id(),
            $request->input('notes'),
        );

        app(ActivityLogServiceInterface::class)->log(
            auth()->id(), 'payment_rejected', $payment,
            "Payment rejected: {$payment->amount} {$payment->currency}",
            ['gateway' => $methodKey, 'reason' => $request->input('notes')]
        );

        return redirect()->back()->with('success', __('super-admin.payment_rejected'));
    }
}
