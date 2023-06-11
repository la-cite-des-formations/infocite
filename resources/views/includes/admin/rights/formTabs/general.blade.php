<div class="col">
  @error('right.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-2 px-0 mr-2 text-right my-auto" for="right-name">Identifiant</label>
        <input id="right-name" wire:model="right.name" type="input"
               class="col-8 form-control" placeholder="Identifiant système">
    </div>
  @error('right.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row mb-2">
        <label class="col-2 px-0 mr-2 text-right my-auto" for="right-description">Description</label>
        <textarea id="right-description" wire:model="right.description" type="text" rows="1"
                  class="col-8 form-control" placeholder="Description des droits utilisateurs">
        </textarea>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-2">Rôle</div>
        <div class="col-5">Description</div>
        <div class="col ml-2">Rôle exercé :</div>
    </div>
  @foreach ($roles as $role)
   @error("right.{$role->description}")
    @include('includes.rules-error-message', ['labelsColLg' => 'col-7'])
   @enderror
    <div class="form-group row">
        <label class="col-2 px-0 mr-2 text-right my-auto" for="role-title-{{ $role->id }}">{{ $role->name }}</label>
        <textarea id="role-title-{{ $role->id }}" wire:model="right.{{ $role->title }}" type="input" rows="2"
                  class="col-2 mr-2 form-control" placeholder="{{ $role->placeholder }}">
        </textarea>
        <textarea id="role-description-{{ $role->id }}" wire:model="right.{{ $role->description }}" type="text" rows="2"
                  class="col-5 form-control" placeholder="Description du rôle">
        </textarea>
        <div class="col px-0 ml-2">
            <div class="form-check">
                <input
                    wire:model="defaultRolesCbx.{{ $role->id }}"
                    type="checkbox"
                    class="form-check-input role-cbx"
                    id="dfroles-cbx-{{ $role->id }}">
                <label for="dfroles-cbx-{{ $role->id }}" class="form-check-label">Par défaut</label>
            </div>
            <div class="form-check">
                <input
                    wire:model="dashboardRolesCbx.{{ $role->id }}"
                    type="checkbox"
                    class="form-check-input role-cbx"
                    id="dbroles-cbx-{{ $role->id }}">
                <label for="dbroles-cbx-{{ $role->id }}" class="form-check-label">Via le tableau de bord</label>
            </div>
        </div>
    </div>
  @endforeach
</div>
