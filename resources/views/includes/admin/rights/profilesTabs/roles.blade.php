    <legend class="col-form-label">Rôles à attribuer</legend>
  @foreach ($roles as $role)
    <div class="form-check">
        <input
            wire:model="profilesRolesCbx.{{ $role->id }}" type="checkbox"
            class="form-check-input role-cbx" id="profiles-cbx-{{ $role->id }}">
        <label for="profiles-cbx-{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
    </div>
  @endforeach
