    <legend class="col-form-label">Rôles à attribuer</legend>
  @foreach ($roles as $role)
    <div class="form-check ml-1">
        <input
            wire:model="usersRolesCbx.{{ $role->id }}"
            type="checkbox"
            class="form-check-input role-cbx"
            id="users-cbx-{{ $role->id }}">
        <label for="users-cbx-{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
    </div>
  @endforeach
