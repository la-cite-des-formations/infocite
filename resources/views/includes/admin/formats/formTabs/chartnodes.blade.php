<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les nœuds à dissocier"
                   for="format-chartnodes" class="m-auto py-2">Nœuds correspondant</label>
            <select id="format-chartnodes" multiple wire:model="selectedRelatedChartnodes"
                    class="form-select flex-fill" size="10">
              @foreach($format->chartnodes as $chartnode)
                <option value="{{ $chartnode->id }}">{{ $chartnode->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title="Associer les nœuds disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title="Dissocier les nœuds correspondant sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les nœuds à associer"
                   for="format-available-chartnodes" class="m-auto py-2">Nœuds disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-text text-secondary" title="Rechercher un nœud graphique">
                    <span class="material-icons md-18">search</span>
                </div>
                <input wire:model="chartnodeSearch" class="form-control" id="format-searched-chartnodes">
            </div>
            <select id="format-available-chartnodes" multiple wire:model="selectedAvailableChartnodes"
                    class="form-select flex-fill" size="8">
              @foreach($availableChartnodes as $chartnode)
                <option value="{{ $chartnode->id }}">
                    {{ $chartnode->name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
