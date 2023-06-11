<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="Sélectionner les profils à dissocier"
                   for="user-linked-profiles">Profils associés</label>
            <select id="user-linked-profiles" multiple wire:model="selectedLinkedProfiles" class="form-control" size="10">
              @foreach($user->profiles as $profile)
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
            <button wire:click="applyProfiles" class="d-flex btn btn-sm btn-primary p-0 mt-3" type="button"
                    title ="Appliquer les profils sélectionnés (associés ou disponibles)">
                <span class="material-icons md-18">layers</span>
            </button>
        </div>
        <div class="form-group col mt-3">
            <label title="Sélectionner les profils à ajouter"
                   for="user-available-profiles">Profils disponibles</label>
            <select id="user-available-profiles" multiple wire:model="selectedAvailableProfiles"
                    class="form-control" size="10">
              @foreach($availableProfiles as $profile)
                <option value="{{ $profile->id }}">
                    {{ $profile->first_name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
