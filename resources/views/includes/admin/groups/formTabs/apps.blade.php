<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="Sélectionner les applications à retirer"
                   for="group-linked-apps">Applications spécifiques</label>
            <select id="group-linked-apps" multiple wire:model="selectedLinkedApps" class="form-control" size="10">
              @foreach($group->apps as $app)
                <option value="{{ $app->id }}">
                    {{ $app->name }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Ajouter les applications disponibles sélectionnées aux applications spécifiques">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les applications spécifiques sélectionnées">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-3">
            <label title="Sélectionner les applications à ajouter"
                   for="group-available-apps">Applications disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher une application">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input id="group-searched-app" wire:model="appSearch" class="form-control">
            </div>
            <select id="group-available-apps" multiple wire:model="selectedAvailableApps"
                    class="form-control" size="8">
              @foreach($availableApps as $app)
                <option value="{{ $app->id }}">
                    {{ $app->name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
