<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="Sélectionner les utilisateurs à retirer"
                   for="app-linked-users">Utilisateurs spécifiques</label>
            <select id="app-linked-users" multiple wire:model="selectedLinkedUsers" class="form-control" size="10">
              @foreach($app->users as $user)
                <option value="{{ $user->id }}">
                    {{ "$user->first_name $user->name" }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Ajouter les utilisateurs disponibles sélectionnés aux utilisateurs spécifiques">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les utilisateurs spécifiques sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-3">
            <label title="Sélectionner les utilisateurs à ajouter"
                   for="app-available-users">Utilisateurs disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un utilisateur">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input id="app-searched-user" wire:model="userSearch" class="form-control">
            </div>
            <select id="app-available-users" multiple wire:model="selectedAvailableUsers"
                    class="form-control" size="8">
              @foreach($availableUsers as $user)
                <option value="{{ $user->id }}">
                    {{ "{$user->first_name} {$user->name}" }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
