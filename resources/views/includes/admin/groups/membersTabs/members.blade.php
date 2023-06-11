<select
    id="group-available-users"
    multiple wire:model="selectedAvailableUsers"
    class="form-control rounded-top-0"
    size="10">
  @foreach($availableUsers as $user)
    <option @if($user->is_frozen) class="alert-warning" @endif value="{{ $user->id }}">
        {{ "$user->name $user->first_name" }}
    </option>
  @endforeach
</select>
