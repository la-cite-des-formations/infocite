<div class="col py-0 pl-0 pr-1">
    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="chartnode-name">Nom</label>
        <input id="chartnode-name" wire:model="chartnode.name" type="input"
            class="col form-control" placeholder="Nom du noeud graphique">
    </div>
  @error('chartnode.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="process-group">Service / Fonction</label>
        <select id="process-group" wire:model="chartnode.code_fonction" type="input" class="col form-control">
            <option label="Choisir le service ou la fonction de rattachement..."></option>
          @foreach($groups as $group)
            <option value='{{ $group->code_ypareo }}'>{{ $group->name }}</option>
          @endforeach
        </select>
    </div>
   @error('chartnode.code_fonction')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
   @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="chartnode-parent">Parent</label>
        <select id="chartnode-parent" wire:model="chartnode.parent_id" type="input" class="col form-control">
            <option label="Choisir le noeud graphique parent..."></option>
          @foreach($parents as $parent)
            <option value='{{ $parent->id }}'>{{ $parent->name }}</option>
          @endforeach
        </select>
    </div>
  @error('chartnode.parent_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="chartnode-format">Mise en forme</label>
        <select id="chartnode-format" wire:model="chartnode.format_id" type="input" class="col form-control">
            <option label="Choisir la mise en forme..."></option>
          @foreach($formats as $format)
            <option value='{{ $format->id }}' class='alert alert-{{ $format->style }}'>{{ $format->name }}</option>
          @endforeach
        </select>
    </div>
  @error('chartnode.format_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror

    <div class="form-group row flex-fill mt-2 mb-1 mx-0">
        <label class="col-3 text-right my-auto pr-2" for="chartnode-rank">Classement</label>
        <input id="chartnode-rank" wire:model="chartnode.rank" type="input"
            class="col form-control" placeholder="Ordre de classement pour l'affichage">
    </div>
  @error('chartnode.rank')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
</div>
