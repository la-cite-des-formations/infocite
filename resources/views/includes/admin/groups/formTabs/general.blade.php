<div class="col pt-2">
  @error('group.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="group-name">Nom</label>
        <div class="col-7">
            <input  id="group-name" wire:model="group.name" type="input"
                    class="form-control" placeholder="Nom du groupe">
        </div>
    </div>
  @error('group.public')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="group-public">
            Libellé public
            <span class="d-block small text-muted font-weight-normal text-end" style="font-size: 0.75rem;">(Optionnel)</span>
        </label>
        <div class="col-7">
            <input  id="group-public" wire:model="group.public" type="input"
                    class="form-control" placeholder="Nom explicite pour l'affichage (ex: Tous les utilisateurs)">
        </div>
    </div>
  @error('group.type')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="group-type">Type</label>
        <div class="col-7">
            <select id="group-type" wire:model="group.type" type="input" class="form-select">
                <option label="Choisir le type..."></option>
              @foreach(AP::getGroupTypes() as $typeKey => $typeName)
                <option value='{{ $typeKey }}'>{{ $typeName }}</option>
              @endforeach
            </select>

        </div>
    </div>
</div>
