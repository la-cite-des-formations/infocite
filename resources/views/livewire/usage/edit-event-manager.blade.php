<section wire:key='eventEdit' id="lw-edit-event" class="services section-bg">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2 class="title-icon">
                <i class="material-icons-outlined md-36 text-primary">calendar_today</i>
                Événement - {{ $mode === 'edition' ? 'Édition' : 'Création' }}
            </h2>
            <p>Ce formulaire vous permet de {{ $mode === 'edition' ? 'modifier' : 'créer' }} un événement dans l'agenda
                de l'intranet.</p>
        </div>

        <form wire:submit.prevent="save" id="eventForm" class="mx-auto" style="max-width: 900px;">
            @includeWhen(session()->has('message'), 'includes.confirm-message')

            {{-- Erreurs de validation globales si présentes --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4 shadow-sm">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Titre de l'événement --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="event-title" class="form-label fw-bold">
                            Titre de l'événement <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="event-title" wire:model.defer="title"
                            class="form-control form-control @error('title') is-invalid @enderror"
                            placeholder="Ex: Séminaire pédagogique de rentrée, Soirée des apprentis...">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="event-description" class="form-label fw-bold">
                            Description de l'événement <span class="text-danger">*</span>
                        </label>
                        <textarea id="event-description" wire:model.defer="description" rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Précisez le déroulé, le public concerné ou les informations clés..."></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type d'événement --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="event-type" class="form-label fw-bold">
                                Type d'événement <span class="text-danger">*</span>
                            </label>
                            <select id="event-type" wire:model.defer="event_type_id"
                                class="form-select @error('event_type_id') is-invalid @enderror">
                                <option value="">-- Choisir un type d'événement --</option>
                                @foreach ($eventTypes as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Lieu --}}
                        <div class="col-md-6">
                            <label for="event-location" class="form-label fw-bold">Lieu / Salle</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-map text-danger"></i></span>
                                <input type="text" id="event-location" wire:model.defer="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    placeholder="Ex: Amphithéâtre, Salle B12, Extérieur...">
                            </div>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Dates et Heures --}}
                    <div class="row mb-3 g-3">
                        <div class="col-md-3">
                            <label for="event-start-date" class="form-label fw-bold">
                                Date de début <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="event-start-date" wire:model.defer="start_date"
                                class="form-control @error('start_date') is-invalid @enderror">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="event-start-time" class="form-label fw-bold">Heure de début</label>
                            <input type="time" id="event-start-time" wire:model.defer="start_time"
                                class="form-control @error('start_time') is-invalid @enderror">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="event-end-date" class="form-label fw-bold">Date de fin</label>
                            <input type="date" id="event-end-date" wire:model.defer="end_date"
                                class="form-control @error('end_date') is-invalid @enderror">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="event-end-time" class="form-label fw-bold">Heure de fin</label>
                            <input type="time" id="event-end-time" wire:model.defer="end_time"
                                class="form-control @error('end_time') is-invalid @enderror">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Article Info Cité associé (Optionnel) --}}
                    <div class="mb-3">
                        <label for="event-post-id" class="form-label fw-bold">
                            Lier à un article Info Cité (Optionnel)
                        </label>
                        <select id="event-post-id" wire:model.defer="post_id"
                            class="form-select @error('post_id') is-invalid @enderror">
                            <option value="">-- Aucun (Événement autonome uniquement dans l'agenda) --</option>
                            @foreach ($availablePosts as $post)
                                <option value="{{ $post->id }}">
                                    [{{ $post->rubric?->name ?? 'Article' }}] {{ $post->title }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">
                            <i class="bx bx-info-circle"></i> Permet d'ajouter un bouton "Voir l'article" sur la carte
                            de l'événement dans la timeline de l'agenda.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vignette événementielle et Recadrage (Focal Point) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bx bx-image me-1 text-primary align-middle"></i>
                        Vignette / Image de l'événement (Optionnel)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        Vous pouvez associer une image d'illustration à cet événement et ajuster son cadrage grâce au
                        point focal.
                    </p>

                    @php
                        $currentPreview = null;
                        if ($uploadedImageUrl) {
                            $currentPreview = $uploadedImageUrl;
                        } elseif ($existingImage) {
                            $currentPreview = $existingImage['thumbnail'] ?? ($existingImage['path'] ?? null);
                        }
                    @endphp

                    @if ($currentPreview)
                        <div
                            class="d-flex flex-column flex-sm-row align-items-center gap-3 mb-3 p-3 bg-light rounded-3 border">
                            <div class="position-relative overflow-hidden rounded shadow-sm"
                                style="width: 160px; height: 120px; flex-shrink: 0; background-color: #000;">
                                <img src="{{ $currentPreview }}" alt="Aperçu vignette" class="w-100 h-100"
                                    style="object-fit: cover; object-position: {{ $focalX }}% {{ $focalY }}%;">
                                <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 start-0 m-1"
                                    style="font-size: 0.7rem;">
                                    {{ round($focalX) }}% / {{ round($focalY) }}%
                                </span>
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">Image sélectionnée</h6>
                                <p class="text-muted small mb-2">
                                    Point focal actuel : <strong>X={{ round($focalX) }}%,
                                        Y={{ round($focalY) }}%</strong>
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-preview-url="{{ $currentPreview }}" data-focal-x="{{ $focalX }}"
                                        data-focal-y="{{ $focalY }}"
                                        onclick="openEventFocalPicker(this.dataset.previewUrl, parseFloat(this.dataset.focalX), parseFloat(this.dataset.focalY))">
                                        <i class="bx bx-crop me-1 align-middle"></i> Ajuster le cadrage
                                    </button>
                                    <button type="button" wire:click="removeImage"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="bx bx-trash me-1 align-middle"></i> Retirer l'image
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-2">
                        <label for="event-image-upload" class="form-label fw-bold">
                            {{ $currentPreview ? 'Remplacer par une autre image' : 'Téléverser une image' }}
                        </label>
                        <input type="file" id="event-image-upload" wire:model="uploadedImage" accept="image/*"
                            class="form-control @error('uploadedImage') is-invalid @enderror">
                        @error('uploadedImage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div wire:loading wire:target="uploadedImage" class="text-primary small mt-1">
                            <i class="bx bx-loader-alt bx-spin"></i> Téléversement de l'image en cours...
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="d-flex justify-content-end align-items-center mb-5">
                <a href="{{ $agendaRubric ? route('rubric.index', ['rubric' => $agendaRubric->segmentPath()]) : '/' }}"
                    class="btn btn-secondary me-1" title="Revenir à l'agenda sans enregistrer">
                    Annuler
                </a>

                <button type="submit" class="btn btn-primary"
                    title="Enregistrer les modifications et revenir à l'agenda">
                    {{ $mode === 'edition' ? 'Modifier' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Script pour la modale de recadrage Focal Point --}}
    <script>
        function openEventFocalPicker(imagePath, focalX, focalY) {
            var titleInput = document.getElementById('event-title');
            var descInput = document.getElementById('event-description');

            Livewire.emit('show', {
                component: 'focal-point-picker',
                data: {
                    imagePath: imagePath,
                    focalX: focalX,
                    focalY: focalY,
                    targetComponent: 'usage.edit-event-manager',
                    postTitle: titleInput ? titleInput.value : 'Aperçu événement',
                    postIcon: 'event',
                    postExcerpt: descInput ? descInput.value.substring(0, 150) : '',
                }
            });
        }
    </script>
</section>
