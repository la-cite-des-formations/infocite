<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les profils à dissocier"
                   for="user-linked-profiles" class="m-auto py-2">Profils associés</label>
            <select id="user-linked-profiles" multiple wire:model="selectedLinkedProfiles"
                    class="form-select flex-fill" size="10">
              @foreach($user->profiles as $profile)
                <option value="{{ $profile->id }}">{{ $profile->first_name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex flex-column col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Associer les profils disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les profils associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
            <button wire:click="applyProfiles" class="d-flex btn btn-sm btn-primary p-1 mt-3 mx-auto" type="button"
                    title ="Appliquer les profils sélectionnés (associés ou disponibles)">
                <span class="material-icons md-18">layers</span>
            </button>
        </div>
        <div class="col mt-1 pt-1">
            <label title="Sélectionner les profils à associer"
                   for="user-available-profiles" class="m-auto py-2">Profils disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-text text-secondary" title="Rechercher un profil">
                    <span class="material-icons md-18">search</span>
                </div>
                <input wire:model="profileSearch" class="form-control" id="user-searched-profile">
            </div>
            <select id="user-available-profiles" multiple wire:model="selectedAvailableProfiles"
                    class="form-select flex-fill" size="8">
              @foreach($availableProfiles as $profile)
                <option value="{{ $profile->id }}">{{ $profile->first_name }}</option>
              @endforeach
            </select>
        </div>
    </div>
</div>
