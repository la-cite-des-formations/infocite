<div class="col pt-2">
  @error('user.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="user-name">Nom</label>
        <div class="col-7">
            <input  id="user-name" wire:model="user.name" type="input"
                    class="form-control" placeholder="Nom de l'utilisateur">
        </div>
    </div>
  @error('user.first_name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="user-firstname">Prénom</label>
        <div class="col-7">
            <input  id="user-firstname" wire:model="user.first_name" type="input"
                    class="form-control" placeholder="Prénom de l'utilisateur">
        </div>
    </div>
  @error('user.email')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="user-email">Email</label>
        <div class="col-7">
            <input  id="user-email" wire:model="user.email" type="email"
                    class="form-control" placeholder="Adresse email de l'utilisateur">
        </div>
    </div>
  @error('user.password')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="user-password">Mot de passe</label>
        <div class="col-7">
            <input  id="user-password" wire:model="user.password" type="password"
                    class="form-control" placeholder="Mot de passe initial">
        </div>
    </div>
</div>
