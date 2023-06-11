<select
    id="profile-available-groups"
    multiple wire:model="selectedAvailableGroups"
    class="form-control rounded-top-0"
    size="10">
  @foreach($availableGroups as $group)
    <option value="{{ $group->id }}">
        {{ $group->name }}
    </option>
  @endforeach
</select>
