<div>
    <div class="gallery-manager mt-3 p-3 bg-light rounded border shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <div class="d-flex align-items-center">
                <div class="text-muted small text-uppercase fw-bold">
                    <i class="bx bx-images me-1"></i>Galerie Photos
                </div>

                {{-- Indicateur de chargement --}}
                <div wire:loading wire:target="newImages" class="ms-3">
                    <div class="d-flex align-items-center">
                        <span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span>
                        <span class="text-primary small fw-semibold">Chargement...</span>
                    </div>
                </div>
            </div>

            {{-- Zone d'upload compacte --}}
            <div class="d-flex align-items-center">
                <label for="gallery-upload-input" class="btn btn-sm btn-primary d-flex align-items-center mb-0" style="cursor: pointer;">
                    <i class="bx bx-plus-circle me-1"></i> Ajouter des images
                </label>
                <input id="gallery-upload-input" type="file" wire:model="newImages" multiple accept="image/*" class="d-none">

                <div wire:ignore class="ms-2">
                    <i class="bx bx-info-circle text-muted fs-5" 
                       style="cursor: help;"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="left" 
                       data-bs-html="true"
                       title="<div class='text-start'>Formats acceptés : JPG, PNG, GIF, WebP<br>Poids maximum : 10 Mo par image</div>"></i>
                </div>
            </div>
        </div>

        {{-- Erreurs de validation --}}
        @error('newImages.*')
            <div class="alert alert-danger py-2 px-3 small">{{ $message }}</div>
        @enderror

        {{-- Vignettes existantes ou temporaires --}}
        @if ($hasImages)
            <div class="gallery-thumbnails">
                <div class="row g-2">
                    @foreach ($imagesList as $image)
                        <div class="col-3 col-md-2 position-relative gallery-thumb-wrapper">
                            <div class="gallery-thumb rounded overflow-hidden shadow-sm"
                                 style="aspect-ratio: 1; position: relative;">
                                <img src="{{ $image['path'] }}" alt="{{ $image['filename'] }}"
                                     class="w-100 h-100" style="object-fit: cover;">

                                {{-- Actions sur la vignette --}}
                                <div class="gallery-thumb-actions position-absolute top-0 end-0 p-1 d-flex gap-1">
                                    @if ($image['order'] > 1)
                                        <button type="button" wire:click="moveUp({{ $image['order'] }})"
                                                class="btn btn-sm btn-light shadow-sm" title="Monter"
                                                style="padding: 0 4px; line-height: 1.4; font-size: 0.75rem;">
                                            <i class="bx bx-chevron-left"></i>
                                        </button>
                                    @endif
                                    @if ($image['order'] < count($imagesList))
                                        <button type="button" wire:click="moveDown({{ $image['order'] }})"
                                                class="btn btn-sm btn-light shadow-sm" title="Descendre"
                                                style="padding: 0 4px; line-height: 1.4; font-size: 0.75rem;">
                                            <i class="bx bx-chevron-right"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="gallery-thumb-actions position-absolute bottom-0 end-0 p-1">
                                    <button type="button" wire:click="removeImage('{{ addslashes($image['path']) }}')"
                                            class="btn btn-sm btn-danger shadow-sm" title="Supprimer cette image"
                                            style="padding: 0 4px; line-height: 1.4; font-size: 0.75rem;">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center mt-1">
                                <span class="text-muted" style="font-size: 0.65rem;">{{ Str::limit($image['filename'], 15) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-muted small mt-2">
                    <i class="bx bx-info-circle me-1"></i>{{ count($imagesList) }} image{{ count($imagesList) > 1 ? 's' : '' }} dans la galerie
                </div>
            </div>
        @else
            <div class="text-center text-muted py-2">
                <span class="small fst-italic">Aucune image dans la galerie</span>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</div>
