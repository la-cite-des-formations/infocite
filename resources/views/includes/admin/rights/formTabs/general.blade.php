<div class="col pt-2">
  @error('right.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-2 col-form-label text-end" for="right-name">Identifiant</label>
        <div class="col-3">
            <input  id="right-name" wire:model="right.name" type="input"
                    class="form-control" placeholder="Identifiant système">
     </div>
    </div>
  @error('right.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-2 col-form-label text-end" for="right-description">Description</label>
        <div class="col-7">
            <textarea id="right-description" wire:model="right.description" type="text" rows="1"
                  class="form-control" placeholder="Description des droits utilisateurs">
            </textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-2">Rôle</div>
        <div class="col-5">Description</div>
        <div class="col ps-0">Rôle exercé :</div>
    </div>
  @foreach ($roles as $role)
   @error("right.{$role->description}")
    @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
   @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-2 col-form-label text-end" for="role-title-{{ $role->id }}">{{ $role->name }}</label>
        <div class="col-2">
            <textarea id="role-title-{{ $role->id }}" wire:model="right.{{ $role->title }}" type="input" rows="2"
                class="form-control" placeholder="{{ $role->placeholder }}">
            </textarea>
        </div>
        <div class="col-5">
            <textarea id="role-description-{{ $role->id }}" wire:model="right.{{ $role->description }}" type="text" rows="2"
                  class="form-control" placeholder="Description du rôle">
            </textarea>
        </div>
        <div class="col">
            <div class="form-check">
                <input  wire:model="defaultRolesCbx.{{ $role->id }}" type="checkbox"
                        class="form-check-input role-cbx" id="df-roles-cbx-{{ $role->id }}">
                <label for="df-roles-cbx-{{ $role->id }}" class="form-check-label">Par défaut</label>
            </div>
            <div class="form-check">
                <input  wire:model="dashboardRolesCbx.{{ $role->id }}" type="checkbox"
                        class="form-check-input role-cbx" id="db-roles-cbx-{{ $role->id }}">
                <label for="db-roles-cbx-{{ $role->id }}" class="form-check-label">Via le tableau de bord</label>
            </div>
        </div>
    </div>
  @endforeach
</div>
