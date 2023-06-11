<div class="col py-0 pl-0 pr-1">
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="actor-manager">Responsable</label>
        <select id="actor-manager" wire:model="manager_id" type="input" class="col form-control">
            <option label="Choisir le responsable hiérarchique..."></option>
          @foreach($managers as $manager)
            <option value='{{ $manager->id }}'>{{ $manager->identity() }}</option>
          @endforeach
        </select>
    </div>
  @error('manager_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
</div>
