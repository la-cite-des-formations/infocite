<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les applications à retirer"
                   for="user-linked-apps" class="m-auto py-2">Applications spécifiques</label>
            <select id="user-linked-apps" multiple wire:model="selectedLinkedApps"
                    class="form-select flex-fill" size="10">
              @foreach($user->apps as $app)
                <option value="{{ $app->id }}">{{ $app->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Ajouter les applications disponibles sélectionnées">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les applications spécifiques sélectionnées">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les applications à ajouter"
                   for="user-available-apps" class="m-auto py-2">Applications disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-text text-secondary" title="Rechercher une application">
                    <span class="material-icons md-18">search</span>
                </div>
                <input wire:model="appSearch" class="form-control" id="user-searched-app">
            </div>
            <select id="user-available-apps" multiple wire:model="selectedAvailableApps"
                    class="form-select flex-fill" size="8">
              @foreach($availableApps as $app)
                <option value="{{ $app->id }}">{{ $app->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
</div>
