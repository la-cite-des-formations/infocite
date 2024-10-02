<div class="col">
    <div class="row g-0">
        <div class="col mt-1 pt-1">
            <label title="@if($usersTabs['currentTab'] === 'users') Sélectionner les utilisateurs à retirer @endif
                          @if($usersTabs['currentTab'] === 'roles') Sélectionner les utilisateurs dont les rôles sont à attribuer ou à désattribuer @endif"
                   for="right-users" class="m-auto py-2">Utilisateurs associés</label>
            <select id="right-users" multiple wire:model="selectedAttachedUsers"
                    class="form-select flex-fill" size="15">
              @foreach($right->realUsers as $user)
                <option value="{{ $user->id.$user->rightsResourceable() }}">
                    {{ $user->identity().$user->rightsResourceableString() }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('usersTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="@if($usersTabs['currentTab'] === 'users') Associer les utilisateurs sélectionnés aux droits utilisateurs @endif
                                @if($usersTabs['currentTab'] === 'roles') Attribuer les rôles aux utilisateurs selectionnés @endif">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('usersTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="@if($usersTabs['currentTab'] === 'users') Dissocier les utilisateurs selectionnés des droits utilisateurs @endif
                                @if($usersTabs['currentTab'] === 'roles') Retirer les rôles des utilisateurs selectionnés @endif">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="col mt-1 pt-1">
            @include('includes.tabs', ['tabsSystem' => $usersTabs])
        </div>
    </div>
</div>
