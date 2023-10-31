<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les utilisateurs à dissocier"
                   for="profile-linked-users" class="m-auto py-2">Utilisateurs associés</label>
            <select id="profile-linked-users" multiple wire:model="selectedLinkedUsers" class="form-control flex-fill" size="10">
              @foreach($profile->users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->identity }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex flex-column form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Associer les utilisateurs disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les utilisateurs associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
            <button wire:click="applyProfile" class="d-flex btn btn-sm btn-primary p-0 mt-3" type="button"
                    title ="Appliquer le profil aux utilisateurs sélectionnés (associés ou disponibles)">
                <span class="material-icons md-18">layers</span>
            </button>
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les utilisateurs à ajouter"
                   for="profile-available-users" class="m-auto py-2">Utilisateurs disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un utilisateur">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input wire:model="userSearch" class="form-control" id="profile-searched-user">
            </div>
            <select id="profile-available-users" multiple wire:model="selectedAvailableUsers"
                    class="form-control flex-fill" size="8">
              @foreach($availableUsers as $user)
                <option value="{{ $user->id }}">
                    {{ $user->identity }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
