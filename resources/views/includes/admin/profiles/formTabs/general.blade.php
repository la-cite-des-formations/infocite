<div class="col pt-2">
  @error('profile.first_name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="profile-firstname">Profil</label>
        <input id="profile-firstname" wire:model="profile.first_name" type="input"
                class="col-7 form-control" placeholder="Nom du profil">
    </div>
</div>
