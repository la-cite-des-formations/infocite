    <legend class="col-form-label">Rôles à attribuer</legend>
  @foreach ($roles as $role)
    <div class="form-check">
        <input  wire:model="groupsRolesCbx.{{ $role->id }}" type="checkbox"
                class="form-check-input role-cbx" id="groups-cbx-{{ $role->id }}">
        <label for="groups-cbx-{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
    </div>
  @endforeach
