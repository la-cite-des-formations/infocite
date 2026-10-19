<div>
    <div class="gallery-manager mt-3 p-3 bg-light rounded border shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <div class="text-muted small text-uppercase fw-bold">
                    <i class="bx bx-images me-1"></i>Galerie multimédia
                </div>

                {{-- Indicateurs de chargement --}}
                <div wire:loading wire:target="newImages,newVideo" class="ms-3">
                    <div class="d-flex align-items-center">
                        <span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span>
                        <span class="text-primary small fw-semibold">Chargement en cours...</span>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Bouton d'upload photos --}}
                <label for="gallery-upload-input" class="btn btn-sm btn-primary d-flex align-items-center mb-0" style="cursor: pointer;">
                    <i class="bx bx-image-add me-1"></i> Ajouter des photos
                </label>
                <input id="gallery-upload-input" type="file" wire:model="newImages" multiple accept="image/*" class="d-none">

                {{-- Bouton d'ajout vidéo --}}
                <button type="button" wire:click="toggleVideoForm" class="btn btn-sm {{ $showVideoForm ? 'btn-secondary' : 'btn-outline-danger' }} d-flex align-items-center mb-0">
                    <i class="bx bx-video-plus me-1"></i> {{ $showVideoForm ? 'Fermer l\'ajout vidéo' : 'Ajouter une vidéo' }}
                </button>

                <div wire:ignore class="ms-1">
                    <i class="bx bx-info-circle text-muted fs-5"
                       style="cursor: help;"
                       data-bs-toggle="tooltip"
                       data-bs-placement="left"
                       data-bs-html="true"
                       title="<div class='text-start'>Photos : JPG, PNG, GIF, WebP (max 10 Mo)<br>Vidéos : Liens YouTube, Google Drive ou fichiers MP4/WebM (max 100 Mo)</div>"></i>
                </div>
            </div>
        </div>

        {{-- Panneau d'ajout de vidéo (repliable) --}}
        @if ($showVideoForm)
            <div class="card mb-3 border-danger-subtle shadow-sm">
                <div class="card-header bg-danger-subtle py-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold text-danger">
                        <i class="bx bx-video me-1"></i>Ajout d'une vidéo (YouTube, Google Drive ou fichier MP4)
                    </span>
                    <button type="button" wire:click="resetVideoForm" class="btn-close btn-sm" aria-label="Fermer"></button>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        {{-- Option 1 : Lien YouTube ou Google Drive --}}
                        <div class="col-12 col-md-7 border-end-md pe-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Lien YouTube ou Google Drive
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-link"></i></span>
                                <input type="url" wire:model.defer="videoUrl" wire:keydown.enter="addExternalVideo"
                                       class="form-control"
                                       placeholder="https://www.youtube.com/watch?v=... ou https://drive.google.com/file/d/...">
                                <button type="button" wire:click="addExternalVideo" class="btn btn-danger" wire:loading.attr="disabled" wire:target="addExternalVideo">
                                    <span wire:loading.remove wire:target="addExternalVideo"><i class="bx bx-plus me-1"></i>Ajouter</span>
                                    <span wire:loading wire:target="addExternalVideo">Ajout...</span>
                                </button>
                            </div>
                            <div class="form-text small text-muted">
                                La miniature est extraite automatiquement.
                            </div>
                        </div>

                        {{-- Option 2 : Téléverser un fichier MP4 --}}
                        <div class="col-12 col-md-5 ps-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Ou téléverser un fichier local (MP4 / WebM)
                            </label>
                            <input id="gallery-video-file-input" type="file" accept="video/mp4,video/webm"
                                   class="form-control form-control-sm"
                                   onchange="handleGalleryVideoFile(this)">
                            <div class="form-text small text-muted">
                                Max 100 Mo. La 1ère seconde est capturée comme vignette.
                            </div>
                        </div>
                    </div>

                    @if ($videoError)
                        <div class="alert alert-danger py-1 px-2 mt-2 mb-0 small">
                            <i class="bx bx-error-circle me-1"></i>{{ $videoError }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Erreurs de validation --}}
        @error('newImages.*')
            <div class="alert alert-danger py-2 px-3 small">{{ $message }}</div>
        @enderror
        @error('newVideo')
            <div class="alert alert-danger py-2 px-3 small">{{ $message }}</div>
        @enderror

        {{-- Vignettes existantes ou temporaires --}}
        @if ($hasImages)
            <div class="gallery-thumbnails">
                <div class="row g-2">
                    @foreach ($imagesList as $image)
                        @php
                            $isVid = ($image['type'] ?? 'image') === 'video';
                            $thumbSrc = $image['thumbnail'] ?? $image['path'];
                        @endphp
                        <div class="col-3 col-md-2 position-relative gallery-thumb-wrapper">
                            <div class="gallery-thumb rounded overflow-hidden shadow-sm"
                                 style="aspect-ratio: 1; position: relative;">
                                <img src="{{ $thumbSrc }}" alt="{{ $image['filename'] }}"
                                     class="w-100 h-100" style="object-fit: cover;">

                                {{-- Badge "Vidéo" --}}
                                @if ($isVid)
                                    <div class="position-absolute top-0 start-0 m-1" style="z-index: 2;">
                                        <span class="badge bg-danger shadow-sm d-flex align-items-center gap-1"
                                              style="font-size: 0.65rem; padding: 2px 5px;">
                                            <i class="bx bx-play-circle"></i> Vidéo
                                        </span>
                                    </div>
                                @endif

                                {{-- Actions haut droite : ordre --}}
                                <div class="gallery-thumb-actions position-absolute top-0 end-0 p-1 d-flex gap-1" style="z-index: 3;">
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

                                {{-- Actions bas droite : supprimer + (média 1 uniquement) cadrage --}}
                                <div class="gallery-thumb-actions position-absolute bottom-0 end-0 p-1 d-flex gap-1" style="z-index: 3;">

                                    {{-- Bouton cadrage : sur le premier média (photo ou vidéo) --}}
                                    @if ($image['order'] === 1)
                                        <button type="button"
                                                title="Définir le cadrage de la vignette de l'article"
                                                class="btn btn-sm btn-info shadow-sm"
                                                style="padding: 0 4px; line-height: 1.4; font-size: 0.75rem;"
                                                onclick="openFocalPointPicker(
                                                    '{{ addslashes($thumbSrc) }}',
                                                    {{ $focalX }},
                                                    {{ $focalY }}
                                                )">
                                            <i class="bx bx-crop"></i>
                                        </button>
                                    @endif

                                    <button type="button" wire:click="removeImage('{{ addslashes($image['path']) }}')"
                                             class="btn btn-sm btn-danger shadow-sm" title="Supprimer ce média"
                                             style="padding: 0 4px; line-height: 1.4; font-size: 0.75rem;">
                                         <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Badge "Vignette" sur le premier média --}}
                            @if ($image['order'] === 1)
                                <div class="text-center mt-1">
                                    <span class="badge bg-info text-white" style="font-size: 0.6rem;">Vignette article</span>
                                </div>
                            @endif

                            <div class="text-center mt-1">
                                <span class="text-muted" style="font-size: 0.65rem;" title="{{ $image['filename'] }}">
                                    {{ Str::limit($image['filename'], 15) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-muted small mt-2">
                    <i class="bx bx-info-circle me-1"></i>{{ count($imagesList) }} média{{ count($imagesList) > 1 ? 's' : '' }} dans la galerie
                    @if (count($imagesList) > 1)
                        &nbsp;·&nbsp;<span class="fst-italic">Le premier élément (Vignette) est utilisé comme fond de tuile sur la page d'accueil.</span>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center text-muted py-2">
                <span class="small fst-italic">Aucun média dans la galerie</span>
            </div>
        @endif
    </div>

    {{-- Champs cachés transmettant les métadonnées de l'article à la modale picker --}}
    @if ($postId)
        @php $post = \App\Models\Post::find($postId); @endphp
        @if ($post)
            <input type="hidden" id="fpp-post-title"   value="{{ $post->previewTitle() }}">
            <input type="hidden" id="fpp-post-icon"    value="{{ $post->icon }}">
            <input type="hidden" id="fpp-post-excerpt" value="{{ $post->preview() }}">
        @endif
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        /**
         * Capture une miniature automatique côté navigateur avant upload du fichier vidéo.
         */
        function handleGalleryVideoFile(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            const video = document.createElement('video');
            video.preload = 'metadata';
            video.src = URL.createObjectURL(file);
            video.muted = true;
            video.playsInline = true;
            video.currentTime = 1.0;

            const component = window.livewire.find(input.closest('[wire\\:id]').getAttribute('wire:id'));

            video.onseeked = function () {
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 360;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    URL.revokeObjectURL(video.src);

                    if (component) {
                        component.set('videoThumbnailData', dataUrl);
                        component.upload('newVideo', file);
                    }
                } catch (e) {
                    console.warn('Miniature vidéo automatique non générée:', e);
                    if (component) {
                        component.upload('newVideo', file);
                    }
                }
            };

            video.onerror = function () {
                if (component) {
                    component.upload('newVideo', file);
                }
            };
        }

        /**
         * Ouvre la modale FocalPointPicker via le ModalManager Livewire.
         *
         * @param {string} imagePath  URL publique de la première image ou de la vignette vidéo.
         * @param {number} focalX     Point focal X courant (0–100).
         * @param {number} focalY     Point focal Y courant (0–100).
         */
        function openFocalPointPicker(imagePath, focalX, focalY) {
            Livewire.emit('show', {
                component: 'focal-point-picker',
                data: {
                    imagePath: imagePath,
                    focalX:    focalX,
                    focalY:    focalY,
                    postTitle:   document.getElementById('fpp-post-title')   ? document.getElementById('fpp-post-title').value   : '',
                    postIcon:    document.getElementById('fpp-post-icon')    ? document.getElementById('fpp-post-icon').value    : '',
                    postExcerpt: document.getElementById('fpp-post-excerpt') ? document.getElementById('fpp-post-excerpt').value : '',
                },
                filter: null,
            });
        }
    </script>
</div>
