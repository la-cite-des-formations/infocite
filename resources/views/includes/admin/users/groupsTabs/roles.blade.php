    <legend class="col-form-label">Rôles à attribuer</legend>
  @foreach ($roles->collection as $role)
    <div class="form-check ml-1">
        <input
            wire:model="rolesCheckboxes.{{ $role->id }}"
            type="checkbox"
            class="form-check-input role-cbx"
            id="group-cbx-{{ $role->id }}">
        <label for="group-cbx-{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
    </div>
  @endforeach
