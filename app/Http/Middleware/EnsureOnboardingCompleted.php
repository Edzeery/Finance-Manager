<?php

namespace App\Http\Middleware;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\OnboardingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    protected array $except = [
        'onboarding.*',
        'verification.*',
        'livewire.*',
        'payment.webhook.*',
        'chargily.back',
        'paypal.back',
        'payment.checkout',
        'payment.check-status',
        'payment.status',
        'payment.return',
        'super.admin.*',
        'locale.switch',
        'theme.switch',
        'currency.switch',
        'logout',
        'invitations.accept',
        'invitations.decline',

    ];

    /**
     * ما عدا داخل onбординг لمستخدمين أكملوا onboarding (تجديد، دفع يدوي، تغيير طريقة الدفع).
     */
    protected array $postOnboardingAllowed = [
        'onboarding.manual-proof',
        'payment.status',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if ($request->is('api/*')) {
            return $next($request);
        }

        $routeName = $this->resolveEffectiveRouteName($request);
        $isOnboardingRoute = $routeName && str_starts_with($routeName, 'onboarding.');

        if ($user->hasCompletedOnboarding()) {
            if ($isOnboardingRoute && $routeName !== 'onboarding.complete') {
                if ($routeName && $this->inPostOnboardingAllowed($routeName)) {
                    return $next($request);
                }

                return redirect()->route('dashboard');
            }

            return $next($request);
        }

        if ($routeName && $this->inExceptArray($routeName)) {
            return $next($request);
        }
        Log::debug('Onboarding redirect', ['route' => $routeName, 'referer' => $request->headers->get('referer')]);

        $user = auth()->user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->pending_plan_id) {
            $pendingPayment = Payment::withoutWorkspace()
                ->where('user_id', $user->id)
                ->where('status', PaymentStatus::CheckoutPending->value)
                ->latest()
                ->first();

            if ($pendingPayment && $pendingPayment->verification()->exists()) {
                return redirect()->route('onboarding.manual-proof', $pendingPayment);
            }

            if ($pendingPayment && OnboardingService::isManual($pendingPayment->method)) {
                return redirect()->route('onboarding.manual-proof', $pendingPayment);
            }

            if ($pendingPayment) {
                return redirect()->route('payment.status', $pendingPayment);
            }

            $completedPayment = Payment::withoutWorkspace()
                ->where('user_id', $user->id)
                ->where('status', PaymentStatus::CheckoutPaid->value)
                ->latest()
                ->first();

            if ($completedPayment) {
                return redirect()->route('onboarding.setup');
            }

            return redirect()->route('onboarding.plan');
        }

        return redirect()->route('onboarding.plan');
    }

    /**
     * نقطة Livewire الموحدة (/livewire/update) تحمل اسم route ثابت
     * (livewire.update) بدلاً من اسم الصفحة الفعلية. هذه الدالة تستخرج
     * اسم الـ route الحقيقي من Referer عندما يكون الطلب طلب Livewire AJAX،
     * كي تعمل قائمة $except بشكل صحيح على تفاعلات الصفحة (وليس فقط تحميلها الأول).
     */
    protected function resolveEffectiveRouteName(Request $request): ?string
    {
        $currentRouteName = $request->route()?->getName();

        $isLivewireRequest = $request->hasHeader('X-Livewire')
            || $currentRouteName === 'livewire.update'
            || $currentRouteName === 'livewire.upload-file';

        if (! $isLivewireRequest) {
            return $currentRouteName;
        }

        $referer = $request->headers->get('referer');
        if (! $referer) {
            return $currentRouteName;
        }

        try {
            $refererRequest = Request::create($referer);
            $route = app('router')->getRoutes()->match($refererRequest);

            return $route->getName() ?? $currentRouteName;
        } catch (\Throwable $e) {
            Log::warning('EnsureOnboardingCompleted: failed to match referer route', [
                'referer' => $referer,
                'error' => $e->getMessage(),
            ]);

            return $currentRouteName;
        }
    }

    protected function inExceptArray(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        foreach ($this->except as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -2);
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }

    protected function inPostOnboardingAllowed(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        foreach ($this->postOnboardingAllowed as $pattern) {
            if ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }
}
