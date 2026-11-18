<div class="col pt-2">
    @error('title')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end fw-bold" for="event-title">Titre <span class="text-danger">*</span></label>
        <div class="col-8">
            <input id="event-title" wire:model="title" type="text"
                class="form-control @error('title') is-invalid @enderror" placeholder="Titre de l'événement">
        </div>
    </div>

    @error('event_type_id')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end fw-bold" for="event-type">Type <span class="text-danger">*</span></label>
        <div class="col-8">
            <select id="event-type" wire:model="event_type_id" class="form-select @error('event_type_id') is-invalid @enderror">
                <option value="">-- Choisir un type d'événement --</option>
                @foreach ($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @error('start_date')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    @error('start_time')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end fw-bold" for="event-start-date">Début <span class="text-danger">*</span></label>
        <div class="col-5">
            <input id="event-start-date" wire:model="start_date" type="date"
                class="form-control @error('start_date') is-invalid @enderror">
        </div>
        <div class="col-3">
            <input id="event-start-time" wire:model="start_time" type="time"
                class="form-control @error('start_time') is-invalid @enderror" placeholder="09:00">
        </div>
    </div>

    @error('end_date')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    @error('end_time')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="event-end-date">Fin</label>
        <div class="col-5">
            <input id="event-end-date" wire:model="end_date" type="date"
                class="form-control @error('end_date') is-invalid @enderror">
        </div>
        <div class="col-3">
            <input id="event-end-time" wire:model="end_time" type="time"
                class="form-control @error('end_time') is-invalid @enderror" placeholder="17:00">
        </div>
    </div>

    @error('location')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="event-location">Lieu</label>
        <div class="col-8">
            <input id="event-location" wire:model="location" type="text"
                class="form-control @error('location') is-invalid @enderror" placeholder="Ex: Amphithéâtre, Salle B12, Zoom...">
        </div>
    </div>

    @error('post_id')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="event-post-id">Article lié</label>
        <div class="col-8">
            <select id="event-post-id" wire:model="post_id" class="form-select @error('post_id') is-invalid @enderror">
                <option value="">-- Aucun (Événement autonome) --</option>
                @foreach ($availablePosts as $post)
                    <option value="{{ $post->id }}">{{ $post->title }} (#{{ $post->id }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @error('description')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
    @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end fw-bold" for="event-description">Description <span class="text-danger">*</span></label>
        <div class="col-8">
            <textarea id="event-description" wire:model="description" rows="4"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Description détaillée de l'événement..."></textarea>
        </div>
    </div>
</div>
