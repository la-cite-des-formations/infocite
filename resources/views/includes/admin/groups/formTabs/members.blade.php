<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="@if($membersTabs['currentTab'] === 'members') Sélectionner les membres à retirer @endif
                          @if($membersTabs['currentTab'] === 'function') Sélectionner les membres dont la fonction est à attribuer ou à retirer @endif"
                   for="group-members" class="m-auto py-2">Membres du groupe</label>
            <select id="group-members" multiple wire:model="selectedMembers"
                    class="form-control flex-fill" size="10">
              @foreach($group->users as $member)
                <option @if($member->is_frozen) class="alert-warning" @endif value="{{ $member->id }}">
                    {{ "$member->name $member->first_name" }}
                </option>
               @if ($membersTabs['currentTab'] == 'function' && isset($member->pivot->function))
                <option disabled class="ml-2">{{ "({$member->pivot->function})" }}</option>
               @endif
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('membersTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="@if($membersTabs['currentTab'] === 'members') Ajouter les utilisateurs sélectionnés au groupe @endif
                                @if($membersTabs['currentTab'] === 'function') Attribuer la fonction aux membres selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('membersTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="@if($membersTabs['currentTab'] === 'members') Retirer les membres selectionnés du groupe @endif
                                @if($membersTabs['currentTab'] === 'function') Retirer la fonction des membres selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-1 pt-1">
            @include('includes.tabs', ['tabsSystem' => $membersTabs])
        </div>
    </div>
</div>
