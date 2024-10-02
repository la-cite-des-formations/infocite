<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="@if($groupsTabs['currentTab'] === 'groups') Sélectionner les groupes à retirer @endif
                          @if($groupsTabs['currentTab'] === 'roles') Sélectionner les groupes dont les rôles sont à attribuer ou à désattribuer @endif"
                   for="right-groups" class="m-auto py-2">Groupes associés</label>
            <div class="input-group mb-1">
                <div class="input-group-text text-secondary" title="Type de groupe">
                    <span class="material-icons md-18">category</span>
                </div>
                <select wire:model="groupType" class="form-select" id="profile-group-type">
                  @foreach(AP::getGroupTypes() as $typeKey => $typeName)
                    <option value="{{ $typeKey }}">{{ $typeName }}</option>
                  @endforeach
                </select>
            </div>
            <select id="right-groups" multiple wire:model="selectedAttachedGroups"
                    class="form-select flex-fill" size="13">
              @foreach($right->groupsByType($groupType) as $group)
                <option value="{{ $group->id.$group->rightsResourceable() }}">
                    {{ $group->name.$group->rightsResourceableString() }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('groupsTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="@if($groupsTabs['currentTab'] === 'groups') Associer les groupes sélectionnés aux droits utilisateurs @endif
                                @if($groupsTabs['currentTab'] === 'roles') Attribuer les rôles aux groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('groupsTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="@if($groupsTabs['currentTab'] === 'groups') Dissocier les groupes selectionnés des droits utilisateurs @endif
                                @if($groupsTabs['currentTab'] === 'roles') Retirer les rôles des groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            @include('includes.tabs', ['tabsSystem' => $groupsTabs])
        </div>
    </div>
</div>
