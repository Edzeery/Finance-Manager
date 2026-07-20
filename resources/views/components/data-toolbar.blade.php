@props([
    'entity' => '',
    'routes' => [],
    'showPrint' => true,
    'showExport' => true,
    'showImport' => false,
])

<div class="d-flex gap-2 align-items-center">
    @if($showPrint)
        <button type="button" class="btn btn-sm btn-outline-secondary btn-custom d-print-none" @click="window.print()" title="{{ __('general.print') }}">
            <i class="bi bi-printer"></i>
        </button>
    @endif

    @if($showExport)
        <div class="dropdown d-print-none">
            <button class="btn btn-sm btn-outline-secondary btn-custom dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width:120px">
                <li>
                    <a class="dropdown-item" href="{{ route('data.export', ['entity' => $entity, 'format' => 'xlsx']) . '?' . http_build_query(request()->query()) }}">
                        <i class="bi bi-file-earmark-excel ms-2"></i>Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('data.export', ['entity' => $entity, 'format' => 'csv']) . '?' . http_build_query(request()->query()) }}">
                        <i class="bi bi-file-earmark-text ms-2"></i>CSV
                    </a>
                </li>
            </ul>
        </div>
    @endif

    @if($showImport)
        <button type="button" class="btn btn-sm btn-outline-secondary btn-custom d-print-none" data-bs-toggle="modal" data-bs-target="#importModal" title="{{ __('general.import') }}">
            <i class="bi bi-upload"></i>
        </button>
    @endif

    {{ $slot ?? '' }}
</div>
