<div class="col pt-2">
  @error('post.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-title">Titre</label>
        <div class="col-7">
            <input id="post-title" wire:model="post.title" type="input"
               class="form-control" placeholder="Titre de l'article">
        </div>
    </div>
  @error('post.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-icon">Icône</label>
        @include('includes.icon-picker', ['model' => 'post', 'inAdminInterface' => TRUE])
    </div>
    <div>
      @error('post.content')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
      @enderror
    </div>
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end mb-auto" for="post-content">Contenu</label>
        <div wire:ignore class="col-10">
            <textarea id="post-content" wire:model="post.content" type="input"
                      class="form-control tinymce" placeholder="Contenu de l'article">
            </textarea>
        </div>
    </div>
  @error('post.rubric_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-rubric-id">Rubrique</label>
        <div class="col-5">
            <select id="post-rubric-id" wire:model="post.rubric_id" type="input" class="form-select">
                <option label="Choisir la rubrique..."></option>
              @foreach($rubrics as $rubric)
                <option value='{{ $rubric->id }}'>{{ (is_object($rubric->parent) ? $rubric->parent->name.' / ' : '').$rubric->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
    <div class="row g-2 align-items-center mb-3">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                <strong>Note :</strong> Les modifications de la galerie (envoi, suppression, ordre) ne seront définitivement enregistrées que lorsque vous <strong>sauvegarderez l'article</strong> (bouton en bas de page).
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <livewire:usage.post-gallery :postId="$post->id" :wire:key="'gallery-admin-' . ($post->id ?? 'new')" />
        </div>
    </div>
  @error('post.published')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-published">Publier</div>
        <div class="form-check col ms-1">
            <input  id="post-published" wire:model="post.published"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-acknowledgment">Accusé de réception</div>
        <div class="form-check col ms-1">
            <input  id="post-acknowledgment" wire:model="post.is_acknowledgment_required"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-rating">Système de notation</div>
        <div class="form-check col ms-1">
            <input  id="post-rating" wire:model="post.is_rating_enabled"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
    <div class="row g-2 align-items-center mb-2 mt-4">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card p-3 border-0" style="background-color: #f8fafc; border-radius: 8px;">
                <div class="form-check mb-2">
                    <input id="attach-to-agenda" wire:model="attach_to_agenda" type="checkbox" class="form-check-input">
                    <label class="fw-bold form-check-label" for="attach-to-agenda">Rattacher l'article à l'agenda événementiel</label>
                </div>
                
                @if ($attach_to_agenda)
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="event-type-id" class="form-label small fw-bold">Type d'événement <span class="text-danger">*</span></label>
                            <select id="event-type-id" wire:model="event_type_id" class="form-select form-select-sm">
                                <option value="">Choisir un type...</option>
                                @foreach ($eventTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('event_type_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="event-location" class="form-label small fw-bold">Lieu</label>
                            <input id="event-location" wire:model="location" type="text" class="form-control form-control-sm" placeholder="Ex: Salle de conférence, Zoom, etc.">
                            @error('location') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="event-start-date" class="form-label small fw-bold">Date de début <span class="text-danger">*</span></label>
                            <input id="event-start-date" wire:model="start_date" type="date" class="form-control form-control-sm">
                            @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="event-start-time" class="form-label small fw-bold">Heure de début</label>
                            <input id="event-start-time" wire:model="start_time" type="time" class="form-control form-control-sm">
                            @error('start_time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="event-end-date" class="form-label small fw-bold">Date de fin</label>
                            <input id="event-end-date" wire:model="end_date" type="date" class="form-control form-control-sm">
                            @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="event-end-time" class="form-label small fw-bold">Heure de fin</label>
                            <input id="event-end-time" wire:model="end_time" type="time" class="form-control form-control-sm">
                            @error('end_time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
