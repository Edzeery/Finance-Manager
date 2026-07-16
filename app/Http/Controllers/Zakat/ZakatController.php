<?php

namespace App\Http\Controllers\Zakat;

use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Http\Requests\Zakat\StoreZakatRequest;
use App\Models\ZakatRecord;
use App\Services\ZakatCalculationService;
use Illuminate\Http\Request;

class ZakatController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private ZakatRepositoryInterface $zakatRepo,
    ) {}

    public function calculator(ZakatCalculationService $service)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.zakat', 'zakat.calculator', 'bi-heart-fill');

        $assets = $service->loadUserAssets();

        return view('zakat.calculator', $this->withBreadcrumbs(compact('assets')));
    }

    public function calculate(StoreZakatRequest $request, ZakatCalculationService $service)
    {
        $data = $request->validated();

        $result = $service->calculate($data);

        if (! empty($data['save'])) {
            $record = $this->zakatRepo->create(array_merge($result, [
                'user_id' => auth()->id(),
                'calculation_date' => now(),
                'hijri_year' => now()->format('Y'),
                'notes' => $data['notes'] ?? null,
            ]));

            return redirect()->route('zakat.report', $record)
                ->with('success', __('messages.zakat_saved'));
        }

        return view('zakat.calculator', [
            'result' => $result,
            'assets' => $service->loadUserAssets(),
            'input' => $data,
        ]);
    }

    public function history(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to', 'exceeds_nisab', 'per_page']);

        $records = $this->zakatRepo->history(filters: $filters);

        $tabs = [
            'all' => ['label' => __('general.all')],
            'yes' => ['label' => __('general.yes')],
            'no' => ['label' => __('general.no')],
        ];

        $exceedsNisab = $request->input('exceeds_nisab', 'all');

        return view('zakat.history', compact('records', 'tabs', 'exceedsNisab'));
    }

    public function report(ZakatRecord $zakatRecord)
    {
        $this->authorize('view', $zakatRecord);

        return view('zakat.report', compact('zakatRecord'));
    }
}
