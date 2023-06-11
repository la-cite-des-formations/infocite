<div class="col py-0 pl-0 pr-1">
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-name">Nom</label>
        <input id="process-name" wire:model="process.name" type="input"
            class="col form-control" placeholder="Nom du processus fonctionnel">
    </div>
  @error('process.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror

  @can('createProcessGroup', ['App\\Group'])
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <div class="col-3"></div>
        <div class="form-check">
            <input id="create-process-group" wire:model="createProcessGroup"
                type="checkbox" class="form-check-input">
            <label class="my-auto" for="create-process-group">Créer le service de rattachement</label>
        </div>
    </div>
  @endcan

  @if($createProcessGroup)
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="group-name">Service</label>
        <input id="group-name" wire:model="newProcessGroup.name" type="input"
            class="col form-control" placeholder="Nom du service associé">
    </div>
   @error('newProcessGroup.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
   @enderror

  @else
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-group">Service</label>
        <select id="process-group" wire:model="process.group_id" type="input" class="col form-control">
            <option label="Choisir le service de rattachement..."></option>
            @foreach($groups as $group)
            <option value='{{ $group->id }}'>{{ $group->name }}</option>
            @endforeach
        </select>
    </div>
   @error('process.group_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
   @enderror
  @endif

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-parent">Parent</label>
        <select id="process-parent" wire:model="process.parent_id" type="input" class="col form-control">
            <option label="Choisir le processus parent..."></option>
          @foreach($parents as $parent)
            <option value='{{ $parent->id }}'>{{ $parent->name }}</option>
          @endforeach
        </select>
    </div>
  @error('process.parent_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-manager">Responsable</label>
        <select id="process-manager" wire:model="process.manager_id" type="input" class="col form-control">
            <option label="Choisir le responsable..."></option>
          @foreach($managers as $manager)
            <option value='{{ $manager->id }}'>{{ $manager->identity() }}</option>
          @endforeach
        </select>
    </div>
  @error('process.manager_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-format">Mise en forme</label>
        <select id="process-format" wire:model="process.format_id" type="input" class="col form-control">
            <option label="Choisir la mise en forme..."></option>
          @foreach($formats as $format)
            <option value='{{ $format->id }}' class='alert alert-{{ $format->style }}'>{{ $format->name }}</option>
          @endforeach
        </select>
    </div>
  @error('process.format_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-rank">Classement</label>
        <input id="process-rank" wire:model="process.rank" type="input"
            class="col form-control" placeholder="Ordre de classement pour l'affichage">
    </div>
  @error('process.rank')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
</div>
