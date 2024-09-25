<div class="input-group mb-1">
    <div class="input-group-text text-secondary rounded-top-0" title="Rechercher un groupe">
        <span class="material-icons md-18">search</span>
    </div>
    <input wire:model="userSearch" class="form-control rounded-top-0" id="group-searched-user">
</div>
<select id="group-available-users" multiple wire:model="selectedAvailableUsers"
        class="form-select flex-fill" size="8">
  @foreach($availableUsers as $user)
    <option @if($user->is_frozen) class="alert-warning" @endif value="{{ $user->id }}">{{ "$user->name $user->first_name" }}</option>
  @endforeach
</select>
