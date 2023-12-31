<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les utilisateurs associés à retirer"
                   for="app-linked-users" class="m-auto py-2">Utilisateurs associés</label>
            <select id="app-linked-users" multiple wire:model="selectedLinkedUsers"
                    class="form-control flex-fill" size="10">
              @foreach($app->realUsers as $user)
                <option value="{{ $user->id }}">
                    {{ "$user->first_name $user->name" }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Associer les utilisateurs disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les utilisateurs associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les utilisateurs disponibles à associer"
                   for="app-available-users" class="m-auto py-2">Utilisateurs disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un utilisateur disponible">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input id="app-searched-user" wire:model="userSearch" class="form-control">
            </div>
            <select id="app-available-users" multiple wire:model="selectedAvailableUsers"
                    class="form-control flex-fill" size="8">
              @foreach($availableUsers as $user)
                <option value="{{ $user->id }}">
                    {{ "{$user->first_name} {$user->name}" }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
