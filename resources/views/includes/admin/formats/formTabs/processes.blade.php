<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les processus à dissocier"
                   for="format-processes" class="m-auto py-2">Processus correspondant</label>
            <select id="format-processes" multiple wire:model="selectedRelatedProcesses"
                    class="form-control flex-fill" size="10">
              @foreach($format->processes as $process)
                <option value="{{ $process->id }}">{{ $process->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title="Associer les processus disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title="Dissocier les processus correspondant sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les processus à associer"
                   for="format-available-processes" class="m-auto py-2">Processus disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un processus">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input wire:model="processSearch" class="form-control" id="format-searched-process">
            </div>
            <select id="format-available-processes" multiple wire:model="selectedAvailableProcesses"
                    class="form-control flex-fill" size="8">
              @foreach($availableProcesses as $process)
                <option value="{{ $process->id }}">
                    {{ $process->name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
