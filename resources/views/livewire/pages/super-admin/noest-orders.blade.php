<?php

use App\Models\Payment;
use App\Services\Payments\Noest\NoestService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.super-admin.app')] class extends Component
{
    public array $orders = [];
    public array $wilayas = [];
    public array $desks = [];
    public string $search = '';
    public string $statusFilter = '';
    public string $wilayaFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 25;
    public int $page = 1;
    public array $communes = [];
    public array $selectedOrders = [];
    public bool $loading = true;
    public ?string $error = null;

    public ?string $editingTracking = null;
    public ?array $editData = null;
    public ?string $validatingTracking = null;
    public ?string $deletingTracking = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->loading = true;
        $this->error = null;

        try {
            $service = app(NoestService::class);

            $payments = Payment::withoutWorkspace()
                ->where('method', 'noest')
                ->whereNotNull('transaction_id')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            $orders = [];
            foreach ($payments as $payment) {
                $tracking = $payment->transaction_id;
                $meta = $payment->metadata ?? [];
                $noestResponse = $meta['noest_response'] ?? [];
                $createdData = $noestResponse['data'] ?? $noestResponse;

                $order = [
                    'tracking' => $tracking,
                    'reference' => $payment->reference,
                    'client' => $createdData['client'] ?? $payment->user?->name ?? '',
                    'phone' => $createdData['phone'] ?? '',
                    'phone_2' => $createdData['phone_2'] ?? '',
                    'adresse' => $createdData['adresse'] ?? '',
                    'produit' => $createdData['produit'] ?? 'Finance Manager Subscription',
                    'montant' => (float) ($createdData['montant'] ?? $payment->amount),
                    'wilaya_id' => $createdData['wilaya_id'] ?? '',
                    'wilaya_name' => $createdData['wilaya_name'] ?? '',
                    'commune' => $createdData['commune'] ?? '',
                    'type_id' => (int) ($createdData['type_id'] ?? 1),
                    'stop_desk' => (int) ($createdData['stop_desk'] ?? 0),
                    'station_code' => $createdData['station_code'] ?? '',
                    'remboursement' => (int) ($createdData['remboursement'] ?? 0),
                    'status' => 'upload',
                    'created_at' => $payment->created_at->toDateTimeString(),
                ];

                try {
                    $info = $service->getTrackingInfo($tracking);
                    $infoData = $info['data'] ?? $info;
                    if (is_array($infoData)) {
                        $order = array_merge($order, [
                            'status' => $infoData['status'] ?? $order['status'],
                            'wilaya_name' => $infoData['wilaya_name'] ?? $order['wilaya_name'],
                            'commune' => $infoData['commune'] ?? $order['commune'],
                        ]);
                    }
                } catch (\Exception $e) {
                    // Use default data if tracking info fetch fails
                }

                $orders[] = $order;
            }

            $this->orders = $orders;

            try {
                $wilayasResponse = $service->getWilayas();
                $this->wilayas = $wilayasResponse['data'] ?? $wilayasResponse;
                $desksResponse = $service->getDesks();
                $this->desks = $desksResponse['data'] ?? $desksResponse;
            } catch (\Exception $e) {
                $this->wilayas = [];
                $this->desks = [];
            }
        } catch (\Exception $e) {
            $this->error = __('super-admin.noest_api_error') . ' ' . $e->getMessage();
            $this->orders = [];
        }

        $this->loading = false;
    }

    public function getFilteredOrdersProperty(): array
    {
        $orders = $this->orders;
        $search = strtolower(trim($this->search));

        if ($search !== '') {
            $orders = array_values(array_filter($orders, fn($o) =>
                str_contains(strtolower($o['tracking'] ?? ''), $search)
                || str_contains(strtolower($o['reference'] ?? ''), $search)
                || str_contains(strtolower($o['client'] ?? ''), $search)
                || str_contains($o['phone'] ?? '', $search)
            ));
        }

        if ($this->statusFilter !== '') {
            $orders = array_values(array_filter($orders, fn($o) => ($o['status'] ?? '') === $this->statusFilter));
        }

        if ($this->wilayaFilter !== '') {
            $orders = array_values(array_filter($orders, fn($o) => ($o['wilaya_id'] ?? '') === $this->wilayaFilter));
        }

        usort($orders, function ($a, $b) {
            $cmp = ($a[$this->sortField] ?? '') <=> ($b[$this->sortField] ?? '');
            return $this->sortDirection === 'asc' ? $cmp : -$cmp;
        });

        return $orders;
    }

    public function getPaginatedOrdersProperty(): array
    {
        $filtered = $this->filteredOrders;
        $total = count($filtered);
        $page = max(1, $this->page);
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($filtered, $offset, $this->perPage);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'lastPage' => max(1, (int) ceil($total / $this->perPage)),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $this->perPage, $total),
        ];
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function editOrder(string $tracking): void
    {
        $order = collect($this->orders)->firstWhere('tracking', $tracking);
        if (!$order) return;

        $this->editingTracking = $tracking;
        $this->editData = [
            'client' => $order['client'],
            'phone' => $order['phone'],
            'adresse' => $order['adresse'],
            'commune' => $order['commune'],
            'montant' => $order['montant'],
            'type_id' => $order['type_id'],
            'stop_desk' => $order['stop_desk'],
            'station_code' => $order['station_code'],
            'status' => $order['status'],
            'remboursement' => (int) ($order['remboursement'] ?? 0),
        ];
        $this->loadCommunes($order['wilaya_id'] ?? '');
        $this->dispatch('show-edit-modal');
    }

    public function confirmValidate(string $tracking): void
    {
        $this->validatingTracking = $tracking;
        $this->dispatch('show-validate-modal');
    }

    public function confirmDelete(string $tracking): void
    {
        $this->deletingTracking = $tracking;
        $this->dispatch('show-delete-modal');
    }

    public function saveEdit(): void
    {
        if (!$this->editingTracking || !$this->editData) return;

        try {
            $isShipped = in_array($this->editData['status'], ['fdr_activated', 'livre', 'mise_a_jour']);

            $service = app(NoestService::class);
            $data = [
                'client' => $this->editData['client'],
                'phone' => $this->editData['phone'],
                'adresse' => $this->editData['adresse'],
                'montant' => $this->editData['montant'],
            ];

            if (!$isShipped) {
                $data['commune'] = $this->editData['commune'];
                $data['stop_desk'] = $this->editData['stop_desk'];
                $data['station_code'] = $this->editData['station_code'];
                $service->updateOrderBeforeExpedition($this->editingTracking, $data);
            } else {
                $service->updateOrderInProgress($this->editingTracking, $data);
            }

            $this->dispatch('noest-toast', type: 'success', message: __('super-admin.noest_order_updated'));
            $this->editingTracking = null;
            $this->editData = null;
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('noest-toast', type: 'error', message: $e->getMessage());
        }
    }

    public function loadCommunes(string $wilayaId): void
    {
        try {
            $response = app(NoestService::class)->getCommunes($wilayaId);
            $this->communes = $response['data'] ?? $response;
        } catch (\Exception $e) {
            $this->communes = [];
        }
    }

    public function doValidate(): void
    {
        if (!$this->validatingTracking) return;

        try {
            app(NoestService::class)->validateOrder($this->validatingTracking);
            $this->dispatch('noest-toast', type: 'success', message: __('super-admin.noest_order_validated'));
            $this->validatingTracking = null;
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('noest-toast', type: 'error', message: $e->getMessage());
        }
    }

    public function doDelete(): void
    {
        if (!$this->deletingTracking) return;

        try {
            app(NoestService::class)->deleteOrder($this->deletingTracking);
            $this->dispatch('noest-toast', type: 'success', message: __('super-admin.noest_order_deleted'));
            $this->deletingTracking = null;
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('noest-toast', type: 'error', message: $e->getMessage());
        }
    }

    public function bulkValidate(): void
    {
        if (empty($this->selectedOrders)) return;

        try {
            app(NoestService::class)->validateOrders(array_values($this->selectedOrders));
            $this->dispatch('noest-toast', type: 'success', message: __('super-admin.noest_orders_validated', ['count' => count($this->selectedOrders)]));
            $this->selectedOrders = [];
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('noest-toast', type: 'error', message: $e->getMessage());
        }
    }

    public function downloadLabel(string $tracking): \Illuminate\Http\Response
    {
        try {
            $pdfContent = app(NoestService::class)->getOrderLabel($tracking);
            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, "noest-label-{$tracking}.pdf", ['Content-Type' => 'application/pdf']);
        } catch (\Exception $e) {
            $this->dispatch('noest-toast', type: 'error', message: $e->getMessage());
            return response()->noContent();
        }
    }

    public function exportCsv(): void
    {
        $orders = $this->filteredOrders;
        $lang = app()->getLocale();

        $statusLabels = [
            'upload' => __('super-admin.noest_status_pending'),
            'customer_validation' => __('super-admin.noest_status_validated'),
            'validation_collect_colis' => __('super-admin.noest_status_collected'),
            'fdr_activated' => __('super-admin.noest_status_out_for_delivery'),
            'livre' => __('super-admin.noest_status_delivered'),
            'mise_a_jour' => __('super-admin.noest_status_attempt'),
            'return_asked_by_customer' => __('super-admin.noest_status_return_requested'),
            'return_dispatched_to_partenaire' => __('super-admin.noest_status_return_shipped'),
            'return_recu' => __('super-admin.noest_status_return_received'),
            'colis_suspendu' => __('super-admin.noest_status_suspended'),
            'prepa_expedition' => __('super-admin.noest_status_preparing'),
            'attente_expedition' => __('super-admin.noest_status_waiting_shipment'),
            'verssement_admin_cust' => __('super-admin.noest_status_paid'),
        ];

        $headers = [
            __('super-admin.noest_tracking'),
            __('super-admin.noest_reference'),
            __('super-admin.noest_client'),
            __('super-admin.noest_phone'),
            __('super-admin.noest_product'),
            __('super-admin.noest_amount'),
            __('super-admin.noest_wilaya'),
            __('super-admin.noest_status'),
            __('super-admin.noest_created_at'),
        ];

        $rows = [];
        foreach ($orders as $o) {
            $rows[] = [
                $o['tracking'],
                $o['reference'],
                $o['client'],
                $o['phone'],
                $o['produit'],
                number_format($o['montant'], 2),
                ($o['wilaya_name'] ?: $o['wilaya_id']) . ($o['commune'] ? ' / ' . $o['commune'] : ''),
                $statusLabels[$o['status']] ?? $o['status'],
                $o['created_at'],
            ];
        }

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers);
        foreach ($rows as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $filename = 'noest-orders-' . now()->format('Y-m-d-His') . '.csv';

        $this->dispatch('noest-download-csv', content: base64_encode($content), filename: $filename);
    }
}; ?>

@php
$sl = [
    "upload" => __("super-admin.noest_status_pending"),
    "customer_validation" => __("super-admin.noest_status_validated"),
    "validation_collect_colis" => __("super-admin.noest_status_collected"),
    "fdr_activated" => __("super-admin.noest_status_out_for_delivery"),
    "livre" => __("super-admin.noest_status_delivered"),
    "mise_a_jour" => __("super-admin.noest_status_attempt"),
    "return_asked_by_customer" => __("super-admin.noest_status_return_requested"),
    "return_dispatched_to_partenaire" => __("super-admin.noest_status_return_shipped"),
    "return_recu" => __("super-admin.noest_status_return_received"),
    "colis_suspendu" => __("super-admin.noest_status_suspended"),
    "prepa_expedition" => __("super-admin.noest_status_preparing"),
    "attente_expedition" => __("super-admin.noest_status_waiting_shipment"),
    "verssement_admin_cust" => __("super-admin.noest_status_paid"),
];
$ss = [
    "upload" => "background:var(--warning-light);color:var(--warning)",
    "customer_validation" => "background:var(--info-light);color:var(--info)",
    "validation_collect_colis" => "background:rgba(20,184,166,0.12);color:#0D9488",
    "fdr_activated" => "background:var(--warning-light);color:var(--warning)",
    "livre" => "background:var(--success-light);color:var(--success)",
    "mise_a_jour" => "background:rgba(249,115,22,0.12);color:#EA580C",
    "return_asked_by_customer" => "background:rgba(168,85,247,0.12);color:#9333EA",
    "return_dispatched_to_partenaire" => "background:rgba(99,102,241,0.12);color:#4F46E5",
    "return_recu" => "background:var(--border);color:var(--text-muted)",
    "colis_suspendu" => "background:var(--danger-light);color:var(--danger)",
    "prepa_expedition" => "background:var(--bg-subtle);color:var(--text)",
    "attente_expedition" => "background:var(--bg-subtle);color:var(--text)",
    "verssement_admin_cust" => "background:rgba(5,150,105,0.12);color:#059669",
];
@endphp

    @php
        $allTracking = array_map(fn($o) => $o["tracking"], $this->paginatedOrders["items"] ?? []);
    @endphp
    <div class="data-grid" x-data="noestOrders()"
         data-tracking-numbers="{{ json_encode($allTracking) }}"
         data-copied-message="{{ __('super-admin.noest_copied') }}">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <div class="d-flex flex-wrap align-items-center gap-2">
                <x-search-filter wire-model="search" placeholder="{{ __('general.search') }}..." min-width="200px" />

                    <select wire:model.live="statusFilter" class="form-control" style="width:auto;min-width:120px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        <option value="">{{ __("general.all_status") }}</option>
                        <option value="upload">{{ __("super-admin.noest_status_pending") }}</option>
                        <option value="customer_validation">{{ __("super-admin.noest_status_validated") }}</option>
                        <option value="validation_collect_colis">{{ __("super-admin.noest_status_collected") }}</option>
                        <option value="fdr_activated">{{ __("super-admin.noest_status_out_for_delivery") }}</option>
                        <option value="livre">{{ __("super-admin.noest_status_delivered") }}</option>
                        <option value="mise_a_jour">{{ __("super-admin.noest_status_attempt") }}</option>
                        <option value="return_asked_by_customer">{{ __("super-admin.noest_status_return_requested") }}</option>
                        <option value="return_dispatched_to_partenaire">{{ __("super-admin.noest_status_return_shipped") }}</option>
                        <option value="return_recu">{{ __("super-admin.noest_status_return_received") }}</option>
                        <option value="colis_suspendu">{{ __("super-admin.noest_status_suspended") }}</option>
                        <option value="prepa_expedition">{{ __("super-admin.noest_status_preparing") }}</option>
                        <option value="attente_expedition">{{ __("super-admin.noest_status_waiting_shipment") }}</option>
                        <option value="verssement_admin_cust">{{ __("super-admin.noest_status_paid") }}</option>
                    </select>
                    <select wire:model.live="wilayaFilter" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        <option value="">{{ __("super-admin.noest_all_wilayas") }}</option>
                        @foreach($wilayas as $wilaya)
                            <option value="{{ $wilaya['code'] ?? $wilaya['id'] }}">{{ $wilaya['nom'] ?? $wilaya['name'] }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none;cursor:pointer" wire:click="exportCsv">
                        <i class="bi bi-downloadms-1"></i>{{ __("super-admin.noest_export_csv") }}
                    </button>
                    @if(count($selectedOrders))
                        <button type="button" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--sa-indigo-light);color:var(--sa-indigo);font-weight:600;border:none;cursor:pointer" wire:click="bulkValidate">
                            <i class="bi bi-check2-allms-1"></i>{{ __("super-admin.noest_bulk_validate") }} ({{ count($selectedOrders) }})
                        </button>
                    @endif
                    @if($search || $statusFilter || $wilayaFilter)
                        <button type="button" class="btn" style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer" wire:click="$set('search','');$set('statusFilter','');$set('wilayaFilter','')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">{{ __("general.per_page") }}:</span>
                    <select wire:model.live="perPage" class="form-control" style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        @foreach([10, 15, 25, 50] as $val)
                            <option value="{{ $val }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($loading)
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                    <h4>{{ __("general.loading") }}</h4>
                </div>
            @elseif(!count($this->paginatedOrders["items"]))
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h4>{{ __("super-admin.noest_no_orders") }}</h4>
                    <p>{{ __("super-admin.noest_no_orders_desc") }}</p>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox">
                                <input type="checkbox" class="form-check-input" style="accent-color:var(--accent)" x-on:change="toggleSelectAll($event.target.checked)" :checked="selectedOrders.length === {{ count($this->paginatedOrders["items"]) }} && {{ count($this->paginatedOrders["items"]) }} > 0">
                            </th>
                            <th><a href="#" wire:click.prevent="sortBy('tracking')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none">{{ __("super-admin.noest_tracking") }} @if($sortField === "tracking") <i class="bi bi-arrow-{{ $sortDirection === "asc" ? "up" : "down" }}"></i> @endif</a></th>
                            <th><a href="#" wire:click.prevent="sortBy('reference')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none">{{ __("super-admin.noest_reference") }} @if($sortField === "reference") <i class="bi bi-arrow-{{ $sortDirection === "asc" ? "up" : "down" }}"></i> @endif</a></th>
                            <th>{{ __("super-admin.noest_client") }}</th>
                            <th>{{ __("general.phone") }}</th>
                            <th>{{ __("super-admin.noest_product") }}</th>
                            <th><a href="#" wire:click.prevent="sortBy('montant')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none">{{ __("super-admin.noest_amount") }} @if($sortField === "montant") <i class="bi bi-arrow-{{ $sortDirection === "asc" ? "up" : "down" }}"></i> @endif</a></th>
                            <th>{{ __("super-admin.noest_wilaya") }}</th>
                            <th><a href="#" wire:click.prevent="sortBy('status')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none">{{ __("super-admin.noest_status") }} @if($sortField === "status") <i class="bi bi-arrow-{{ $sortDirection === "asc" ? "up" : "down" }}"></i> @endif</a></th>
                            <th><a href="#" wire:click.prevent="sortBy('created_at')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none">{{ __("super-admin.noest_created_at") }} @if($sortField === "created_at") <i class="bi bi-arrow-{{ $sortDirection === "asc" ? "up" : "down" }}"></i> @endif</a></th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->paginatedOrders["items"] as $order)
                            <tr>
                                <td class="col-checkbox">
                                    <input type="checkbox" class="form-check-input" style="accent-color:var(--accent)" x-model="selectedOrders" value="{{ $order["tracking"] }}">
                                </td>
                                <td>
                                    <a href="#" x-on:click.prevent="copyToClipboard('{{ $order["tracking"] }}')" class="d-flex align-items-center gap-1" style="text-decoration:none" title="{{ __("super-admin.noest_click_to_copy") }}">
                                        <code style="font-size:12px;background:var(--bg-subtle);padding:2px 6px;border-radius:4px;color:var(--sa-indigo)">{{ $order["tracking"] }}</code>
                                        <i class="bi bi-clipboard" style="font-size:11px;color:var(--text-muted)"></i>
                                    </a>
                                </td>
                                <td class="cell-muted" style="font-size:12px">{{ $order["reference"] }}</td>
                                <td>{{ $order["client"] }}</td>
                                <td style="font-size:12px">{{ $order["phone"] }}{{ $order["phone_2"] ? " / " . $order["phone_2"] : "" }}</td>
                                <td style="font-size:12px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $order["produit"] }}">{{ $order["produit"] }}</td>
                                <td><strong>{{ number_format($order["montant"], 2) }}</strong> <span style="font-size:11px;color:var(--text-muted)">DZD</span></td>
                                <td style="font-size:12px">{{ $order["wilaya_name"] }}</td>
                                <td><span class="badge" style="font-size:10px;padding:3px 10px;border-radius:6px;font-weight:600;{{ $ss[$order["status"]] ?? "background:var(--border);color:var(--text-muted)" }}">{{ $sl[$order["status"]] ?? $order["status"] }}</span></td>
                                <td class="cell-muted" style="font-size:12px">{{ $order["created_at"] }}</td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;transition:all 0.15s" title="{{ __("super-admin.noest_edit_title") }}" wire:click="editOrder('{{ $order["tracking"] }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--success);font-size:13px;transition:all 0.15s" title="{{ __("super-admin.noest_validate") }}" wire:click="confirmValidate('{{ $order["tracking"] }}')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);font-size:13px;transition:all 0.15s" title="{{ __("super-admin.noest_download_pdf") }}" wire:click="downloadLabel('{{ $order["tracking"] }}')">
                                            <i class="bi bi-filetype-pdf"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __("general.delete") }}" wire:click="confirmDelete('{{ $order["tracking"] }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if(!$loading && count($this->paginatedOrders["items"]))
            <div class="data-grid-footer">
                <span style="font-size:13px;color:var(--text-muted)">{{ __("general.showing") }} {{ $this->paginatedOrders["from"] }}&ndash;{{ $this->paginatedOrders["to"] }} {{ __("general.of") }} {{ $this->paginatedOrders["total"] }}</span>
                <div class="d-flex align-items-center gap-1">
                    @php $cur = $this->paginatedOrders["page"]; $lst = $this->paginatedOrders["lastPage"]; @endphp
                    @if($cur > 1)
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',1)"><i class="bi bi-chevron-double-left"></i></button>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',{{ $cur - 1 }})"><i class="bi bi-chevron-left"></i></button>
                    @endif
                    @for($p = max(1, $cur - 2); $p <= min($lst, $cur + 2); $p++)
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:{{ $p === $cur ? "none" : "1px solid var(--border)" }};background:{{ $p === $cur ? "var(--accent)" : "transparent" }};color:{{ $p === $cur ? "#0F172A" : "var(--text)" }};font-weight:{{ $p === $cur ? "600" : "400" }};cursor:pointer" wire:click="$set('page',{{ $p }})">{{ $p }}</button>
                    @endfor
                    @if($cur < $lst)
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',{{ $cur + 1 }})"><i class="bi bi-chevron-right"></i></button>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',{{ $lst }})"><i class="bi bi-chevron-double-right"></i></button>
                    @endif
                </div>
            </div>
        @endif

    <div class="modal fade" id="editOrderModal" tabindex="-1" data-bs-backdrop="static" x-data @show-edit-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-pencil-square ms-2" style="color:var(--accent)"></i>{{ __("super-admin.noest_edit_title") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size:12px"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    @if($editingTracking && $editData)
                        @php $isShipped = in_array($editData["status"] ?? "", ["fdr_activated","livre","mise_a_jour"]); @endphp
                        @if($isShipped)
                            <div class="alert alert-warning" style="padding:10px 14px;font-size:12px;border-radius:var(--radius-sm);margin-bottom:16px">
                                <i class="bi bi-info-circle-fillms-1"></i>{{ __("super-admin.noest_edit_shipped_warning") }}
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_tracking") }}</label>
                                <input type="text" class="form-control" value="{{ $editingTracking }}" disabled style="font-size:13px;background:var(--bg-subtle)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_client") }}</label>
                                <input type="text" class="form-control" wire:model="editData.client" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("general.phone") }}</label>
                                <input type="text" class="form-control" wire:model="editData.phone" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("general.address") }}</label>
                                <input type="text" class="form-control" wire:model="editData.adresse" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_amount") }}</label>
                                <input type="number" step="0.01" class="form-control" wire:model="editData.montant" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_delivery_mode") }}</label>
                                <select class="form-control" wire:model="editData.type_id" style="font-size:13px">
                                    <option value="1">{{ __("super-admin.noest_type_delivery") }}</option>
                                    <option value="2">{{ __("super-admin.noest_type_exchange") }}</option>
                                    <option value="3">{{ __("super-admin.noest_type_retrait") }}</option>
                                </select>
                            </div>
                            @if(!$isShipped)
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_wilaya_commune") }}</label>
                                <select class="form-control" wire:model="editData.commune" style="font-size:13px">
                                    <option value="">{{ __("super-admin.noest_select_commune") }}</option>
                                    @foreach($communes as $c)
                                        <option value="{{ $c["commune"] ?? $c }}">{{ $c["commune"] ?? $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_stop_desk") }}</label>
                                <div class="d-flex align-items-center gap-2" style="margin-top:6px">
                                    <x-toggle-switch wire:model="editData.stop_desk" id="stopDeskToggle" :checked="$editData['stop_desk'] ?? false" description="{{ __("super-admin.noest_stop_desk_toggle") }}" />
                                </div>
                                @if($editData["stop_desk"] ?? false)
                                <div style="margin-top:8px">
                                    <label style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:2px">{{ __("super-admin.noest_station_code") }}</label>
                                    <input type="text" class="form-control" wire:model="editData.station_code" style="font-size:13px" placeholder="{{ __("super-admin.noest_select_station") }}">
                                </div>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px">{{ __("super-admin.noest_payment") }}</label>
                                <select class="form-control" wire:model="editData.remboursement" style="font-size:13px">
                                    <option value="0">{{ __("super-admin.noest_payment_cod") }}</option>
                                    <option value="1">{{ __("super-admin.noest_payment_refund") }}</option>
                                    <option value="2">{{ __("super-admin.noest_payment_cod_collect") }}</option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __("general.cancel") }}</button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none" wire:click="saveEdit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEdit">{{ __("general.save") }}</span>
                        <span wire:loading wire:target="saveEdit"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="validateOrderModal" tabindex="-1" data-bs-backdrop="static" x-data @show-validate-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-check2-circle ms-2" style="color:var(--success)"></i>{{ __("super-admin.noest_validate_title") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--success);margin-bottom:12px"><i class="bi bi-question-circle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">{{ __("super-admin.noest_validate_warning") }}</p>
                    <p style="font-size:12px;color:var(--text-muted)">{{ $validatingTracking ? __("super-admin.noest_validate_confirm",["tracking"=>$validatingTracking]) : "" }}</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __("general.cancel") }}</button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--success);color:white;font-weight:600;border:none" wire:click="doValidate" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="doValidate">{{ __("super-admin.noest_validate") }}</span>
                        <span wire:loading wire:target="doValidate"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteOrderModal" tabindex="-1" data-bs-backdrop="static" x-data @show-delete-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash ms-2" style="color:var(--danger)"></i>{{ __("super-admin.noest_delete_title") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">{{ __("super-admin.noest_delete_warning") }}</p>
                    <p style="font-size:12px;color:var(--text-muted)">{{ $deletingTracking ? __("super-admin.noest_delete_confirm",["tracking"=>$deletingTracking]) : "" }}</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __("general.cancel") }}</button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="doDelete" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="doDelete">{{ __("general.delete") }}</span>
                        <span wire:loading wire:target="doDelete"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ show: false, type: 'success', message: '', timer: null }"
         x-on:noest-toast.window="type = $event.detail.type; message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 4000)"
         x-show="show" x-transition.duration.300ms x-cloak
         style="position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;max-width:400px">
        <div :class="'alert alert-' + type" style="padding:12px 16px;border-radius:var(--radius-sm);box-shadow:0 8px 30px rgba(0,0,0,0.12);display:flex;align-items:center;gap:8px;margin-bottom:0" role="alert">
            <i :class="'bi bi-' + (type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-circle-fill' : 'info-circle-fill')"></i>
            <span x-text="message" style="font-size:13px;flex:1"></span>
            <button type="button" class="btn-close" @click="show = false" style="font-size:10px"></button>
        </div>
    </div>
</div>

    @push("scripts")
    <script>
        function initNoestOrders() {
            if (!window._noestToastListener) {
                Livewire.on("noest-toast", (data) => {
                    window.dispatchEvent(new CustomEvent("noest-toast", { detail: data }));
                });
                window._noestToastListener = true;
            }
            if (!window._noestCsvListener) {
                Livewire.on("noest-download-csv", (data) => {
                    const a = document.createElement("a");
                    a.href = "data:text/csv;base64," + data.content;
                    a.download = data.filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });
                window._noestCsvListener = true;
            }
        }
        initNoestOrders();

        function noestOrders() {
            return {
                selectedOrders: [],
                toggleSelectAll(checked) {
                    var el = this.$el.closest('[data-tracking-numbers]');
                    this.selectedOrders = checked && el ? JSON.parse(el.dataset.trackingNumbers) : [];
                },
                copyToClipboard(text) {
                    var el = this.$el.closest('[data-tracking-numbers]');
                    var msg = el ? el.dataset.copiedMessage : 'Copied';
                    navigator.clipboard.writeText(text).then(() => {
                        window.dispatchEvent(new CustomEvent("noest-toast", {
                            detail: { type: "success", message: msg }
                        }));
                    });
                }
            };
        }
    </script>
    @endpush
