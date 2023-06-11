<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="@if($groupsTabs['currentTab'] === 'groups') Sélectionner les groupes à dissocier @endif
                          @if($groupsTabs['currentTab'] === 'function') Sélectionner les groupes dont la fonction associée doit être attribuée ou retirée @endif"
                   for="user-groups">Groupes de l'utilisateur</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Type de groupe">
                        <span class="material-icons md-18">category</span>
                    </div>
                </div>
                <select wire:model="groupType" class="form-control" id="user-group-type">
                  @foreach(AP::getGroupTypes() as $typeKey => $typeName)
                    <option value="{{ $typeKey }}">{{ $typeName }}</option>
                  @endforeach
                </select>
            </div>
            <select id="user-groups" multiple wire:model="selectedUserGroups"
                    class="form-control" size="8">
              @foreach($user->groups([$groupType])->get() as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('groupsTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                    title="@if($groupsTabs['currentTab'] === 'groups') Ajouter les groupes disponibles sélectionnés @endif
                           @if($groupsTabs['currentTab'] === 'function') Attribuer la fonction pour chacun des groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('groupsTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title="@if($groupsTabs['currentTab'] === 'groups') Retirer les groupes de l'utilisateur sélectionnés @endif
                               @if($groupsTabs['currentTab'] === 'function') Retirer la fonction pour chacun des groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        @include('includes.tabs', ['tabsSystem' => $groupsTabs])
    </div>
</div>
