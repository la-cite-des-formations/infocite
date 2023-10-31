<div class="input-group mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text text-secondary rounded-top-0" title="Rechercher un groupe">
            <span class="material-icons md-18">search</span>
        </div>
    </div>
    <input wire:model="groupSearch" class="form-control rounded-top-0" id="user-searched-group">
</div>
<select
    id="profile-available-groups"
    multiple wire:model="selectedAvailableGroups"
    class="form-control flex-fill"
    size="8">
  @foreach($availableGroups as $group)
    <option value="{{ $group->id }}">
        {{ $group->name }}
    </option>
  @endforeach
</select>
