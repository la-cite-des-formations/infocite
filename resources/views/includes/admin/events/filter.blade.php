<!-- Filtre des événements -->
<div class="row w-100 g-3">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text text-secondary material-icons md-18" title="Type d'événement">category</span>
            <select wire:model='filter.event_type_id' class="form-select" id="event-type-filter">
                <option value="">Tous les types...</option>
                @foreach ($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text text-secondary material-icons md-18" title="À partir du">date_range</span>
            <input type="date" wire:model='filter.start_date' class="form-control" placeholder="À partir du...">
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text text-secondary material-icons md-18" title="Jusqu'au">event</span>
            <input type="date" wire:model='filter.end_date' class="form-control" placeholder="Jusqu'au...">
        </div>
    </div>
</div>
