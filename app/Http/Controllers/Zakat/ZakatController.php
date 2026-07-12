<?php

namespace App\Http\Controllers\Zakat;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Requests\Zakat\StoreZakatRequest;
use App\Models\ZakatRecord;
use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Services\ZakatCalculationService;

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

        if (!empty($data['save'])) {
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

    public function history()
    {
        $records = $this->zakatRepo->history();

        return view('zakat.history', compact('records'));
    }

    public function report(ZakatRecord $zakatRecord)
    {
        $this->authorize('view', $zakatRecord);

        return view('zakat.report', compact('zakatRecord'));
    }
}
