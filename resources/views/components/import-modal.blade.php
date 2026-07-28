@props([
    'entity' => '',
    'entityLabel' => '',
])

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('general.import') }} {{ $entityLabel }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('data.import', ['entity' => $entity]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom">{{ __('general.import_file') }}</label>
                        <input type="file" name="file" class="form-custom w-100" accept=".xlsx,.csv" required>
                    </div>
                    <div style="font-size:12px; color:var(--text-muted)">
                        <i class="bi bi-info-circlems-1"></i>
                        {{ __('general.import_hint') }}
                        <a href="{{ route('data.template', ['entity' => $entity]) }}" class="ms-1" style="color:var(--accent)">
                            <i class="bi bi-downloadms-1"></i>{{ __('general.download_template') }}
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-button variant="outline" size="sm" data-bs-dismiss="modal">{{ __('general.cancel') }}</x-button>
                    <x-button submit size="sm">{{ __('general.import') }}</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
