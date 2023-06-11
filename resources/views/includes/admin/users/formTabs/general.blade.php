<div class="col">
  @error('user.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="user-name">Nom</label>
        <input id="user-name" wire:model="user.name" type="input"
                class="col-7 form-control" placeholder="Nom de l'utilisateur">
    </div>
  @error('user.first_name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="user-firstname">Prénom</label>
        <input id="user-firstname" wire:model="user.first_name" type="input"
                class="col-7 form-control" placeholder="Prénom de l'utilisateur">
    </div>
  @error('user.email')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="user-email">Email</label>
        <input id="user-email" wire:model="user.email" type="email"
                class="col-7 form-control" placeholder="Adresse email de l'utilisateur">
    </div>
  @error('user.password')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="user-password">Mot de passe</label>
        <input id="user-password" wire:model="user.password" type="password"
                class="col-7 form-control" placeholder="Mot de passe initial">
    </div>
</div>
