<div class="col pt-2">
  @error('profile.first_name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
  <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="profile-firstname">Profil</label>
        <div class="col-7">
            <input  id="profile-firstname" wire:model="profile.first_name" type="input"
                    class="form-control" placeholder="Nom du profil">
        </div>
    </div>
</div>
