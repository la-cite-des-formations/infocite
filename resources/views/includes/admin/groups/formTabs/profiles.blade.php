<div class="col">
    <div class="form-row">
        <div class="form-group col col mt-1 pt-1">
            <label title="Sélectionner les profils à dissocier"
                   for="group-linked-profiles" class="m-auto py-2">Profils associés</label>
            <select id="group-linked-profiles" multiple wire:model="selectedLinkedProfiles" class="form-control flex-fill" size="10">
              @foreach($group->profiles as $profile)
                <option value="{{ $profile->id }}">
                    {{ $profile->first_name }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex flex-column form-group col-1 my-auto">
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
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les profils à associer"
                   for="group-available-profiles" class="m-auto py-2">Profils disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un profil">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input wire:model="profileSearch" class="form-control" id="group-searched-profile">
            </div>
            <select id="group-available-profiles" multiple wire:model="selectedAvailableProfiles"
                    class="form-control flex-fill" size="8">
              @foreach($availableProfiles as $profile)
                <option value="{{ $profile->id }}">
                    {{ $profile->first_name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
