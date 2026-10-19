<div class="col pt-2">
  @error('eventType.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-2 col-form-label text-end fw-bold" for="event-type-name">Nom</label>
        <div class="col-7">
            <input id="event-type-name" wire:model="eventType.name" type="input"
               class="form-control" placeholder="Nom du type d'événement">
        </div>
    </div>
  @error('eventType.color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-2 col-form-label text-end fw-bold" for="event-type-color">Couleur</label>
        <div class="col-3 d-flex align-items-center">
            <input id="event-type-color" wire:model="eventType.color" type="color"
               class="form-control form-control-color me-2 shadow-sm" style="width: 50px; height: 38px; padding: 6px;">
            <input wire:model="eventType.color" type="text" class="form-control" placeholder="#ffffff" maxlength="7">
        </div>
    </div>
</div>
