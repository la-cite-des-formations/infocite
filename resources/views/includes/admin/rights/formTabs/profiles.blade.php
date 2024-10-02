<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="@if($profilesTabs['currentTab'] === 'profiles') Sélectionner les profils à retirer @endif
                          @if($profilesTabs['currentTab'] === 'roles') Sélectionner les profils dont les rôles sont à attribuer ou à désattribuer @endif"
                   for="right-profiles" class="m-auto py-2">Profils associés</label>
            <select id="right-profiles" multiple wire:model="selectedAttachedProfiles"
                    class="form-select flex-fill" size="15">
              @foreach($right->profiles as $profile)
                <option value="{{ $profile->id.$profile->rightsResourceable() }}">
                    {{ $profile->first_name.$profile->rightsResourceableString() }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('profilesTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="@if($profilesTabs['currentTab'] === 'profiles') Associer les profils sélectionnés @endif
                                @if($profilesTabs['currentTab'] === 'roles') Attribuer les rôles aux profils selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('profilesTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="@if($profilesTabs['currentTab'] === 'profiles') Dissocier les profils selectionnés @endif
                                @if($profilesTabs['currentTab'] === 'roles') Retirer les rôles des profils selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            @include('includes.tabs', ['tabsSystem' => $profilesTabs])
        </div>
    </div>
</div>
