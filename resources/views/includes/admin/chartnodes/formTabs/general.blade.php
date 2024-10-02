<div class="col pt-2">
  @error('chartnode.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="chartnode-name">Nom</label>
        <div class="col">
            <input  id="chartnode-name" wire:model="chartnode.name" type="input"
                    class="form-control" placeholder="Nom du noeud graphique">
        </div>
    </div>
   @error('chartnode.code_fonction')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
   @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="process-group">Fonction</label>
        <div class="col">
            <select id="process-group" wire:model="chartnode.code_fonction" type="input" class="form-select">
                <option label="Choisir le service ou la fonction de rattachement..."></option>
              @foreach($groups as $group)
                <option value='{{ $group->code_ypareo }}'>{{ $group->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('chartnode.parent_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="chartnode-parent">Parent</label>
        <div class="col">
            <select id="chartnode-parent" wire:model="chartnode.parent_id" type="input" class="form-select">
                <option label="Choisir le noeud graphique parent..."></option>
              @foreach($parents as $parent)
                <option value='{{ $parent->id }}'>{{ $parent->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('chartnode.format_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="chartnode-format">Mise en forme</label>
        <div class="col">
            <select id="chartnode-format" wire:model="chartnode.format_id" type="input" class="form-select">
                <option label="Choisir la mise en forme..."></option>
              @foreach($formats as $format)
                <option value='{{ $format->id }}' class='alert alert-{{ $format->style }}'>{{ $format->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('chartnode.rank')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 text-end my-auto pr-2" for="chartnode-rank">Classement</label>
        <div class="col">
            <input  id="chartnode-rank" wire:model="chartnode.rank" type="input"
                    class="form-control" placeholder="Ordre de classement pour l'affichage">
        </div>
    </div>
</div>
