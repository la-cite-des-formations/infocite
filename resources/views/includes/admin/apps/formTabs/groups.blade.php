<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="Sélectionner les groupes associés à retirer"
                   for="app-linked-groups">Groupes associés</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Type de groupe">
                        <span class="material-icons md-18">category</span>
                    </div>
                </div>
                <select id="app-group-type" wire:model="groupType" class="form-control">
                  @foreach(AP::getGroupTypes() as $typeKey => $typeName)
                    <option value="{{ $typeKey }}">{{ $typeName }}</option>
                  @endforeach
                </select>
            </div>
            <select id="app-linked-groups" multiple wire:model="selectedLinkedGroups" class="form-control" size="8">
              @foreach($app->groups([$groupType])->get() as $group)
                <option value="{{ $group->id }}">
                    {{ $group->name }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Associer les groupes disponibles sélectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les groupes associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-3">
            <label title="Sélectionner les groupes disponibles à associer"
                   for="app-available-groups">Groupes disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un groupe disponible">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input id="app-searched-group" wire:model="groupSearch" class="form-control">
            </div>
            <select id="app-available-groups" multiple wire:model="selectedAvailableGroups"
                class="form-control" size="8">
              @foreach($availableGroups as $group)
                <option value="{{ $group->id }}">
                    {{ $group->name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
