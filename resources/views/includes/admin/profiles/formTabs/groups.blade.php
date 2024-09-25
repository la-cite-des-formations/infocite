<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="@if($groupsTabs['currentTab'] === 'groups') Sélectionner les groupes à dissocier @endif
                          @if($groupsTabs['currentTab'] === 'function') Sélectionner les groupes dont la fonction associée doit être attribuée ou retirée @endif"
                   for="profile-groups" class="m-auto py-2">Groupes associés au profil</label>
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
            <select id="profile-groups" multiple wire:model="selectedLinkedGroups"
                    class="form-select flex-fill" size="8">
              @foreach($profile->groups([$groupType])->get() as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
               @if ($groupsTabs['currentTab'] == 'function' && isset($group->pivot->function))
                <option disabled class="ms-2">{{ "({$group->pivot->function})" }}</option>
               @endif
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('groupsTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                    title="@if($groupsTabs['currentTab'] === 'groups') Associer les groupes sélectionnés @endif
                           @if($groupsTabs['currentTab'] === 'function') Attribuer la fonction pour chacun des groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('groupsTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title="@if($groupsTabs['currentTab'] === 'groups') Dissocier les groupes sélectionnés @endif
                               @if($groupsTabs['currentTab'] === 'function') Retirer la fonction pour chacun des groupes selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            @include('includes.tabs', ['tabsSystem' => $groupsTabs])
        </div>
    </div>
</div>
