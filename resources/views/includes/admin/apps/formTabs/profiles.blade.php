<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les profils associés à retirer"
                   for="app-linked-profiles" class="m-auto py-2">Profils associés</label>
            <select id="app-linked-profiles" multiple wire:model="selectedLinkedProfiles"
                    class="form-select flex-fill" size="10">
              @foreach($app->profiles as $profile)
                <option value="{{ $profile->id }}">
                    {{ $profile->first_name }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Associer les profils disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les profils associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les profils disponibles à associer"
                   for="app-available-profiles" class="m-auto py-2">Profils disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-text text-secondary" title="Rechercher un profil disponible">
                    <span class="material-icons md-18">search</span>
                </div>
                <input id="app-searched-profile" wire:model="profileSearch" class="form-control">
            </div>
            <select id="app-available-profiles" multiple wire:model="selectedAvailableProfiles"
                    class="form-select flex-fill" size="8">
              @foreach($availableProfiles as $profile)
                <option value="{{ $profile->id }}">
                    {{ $profile->first_name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
