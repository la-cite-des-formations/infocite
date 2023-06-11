<div class="col">
  @error('group.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="group-name">Nom</label>
        <input id="group-name" wire:model="group.name" type="input" class="col-7 form-control" placeholder="Nom du groupe">
    </div>
  @error('group.type')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="group-type">Type</label>
        <select id="group-type" wire:model="group.type" type="input" class="col-7 form-control">
            <option label="Choisir le type..."></option>
          @foreach(AP::getGroupTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
</div>
