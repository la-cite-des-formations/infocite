<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les groupes à dissocier"
                   for="rubric-groups" class="m-auto py-2">Groupes associés</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Type de groupe">
                        <span class="material-icons md-18">category</span>
                    </div>
                </div>
                <select wire:model="groupType" class="form-control" id="rubric-group-type">
                  @foreach(AP::getGroupTypes() as $typeKey => $typeName)
                    <option value="{{ $typeKey }}">{{ $typeName }}</option>
                  @endforeach
                </select>
            </div>
            <select id="rubric-groups" multiple wire:model="selectedLinkedGroups"
                    class="form-control flex-fill" size="8">
              @foreach($rubric->groups([$groupType])->get() as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title="Associer les groupes disponibles selectionnés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title="Retirer les groupes associé selectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les groupes à associer"
                   for="rubric-available-groups" class="m-auto py-2">Groupes disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un groupe">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input wire:model="groupSearch" class="form-control" id="rubric-searched-group">
            </div>
            <select id="rubric-available-groups" multiple wire:model="selectedAvailableGroups"
                    class="form-control flex-fill" size="8">
              @foreach($availableGroups as $group)
                <option value="{{ $group->id }}">
                    {{ $group->name }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
