<div class="col">
  @error('app.id_owner')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-owner">Propriétaire</label>
        <select id="app-owner" wire:model="app.owner_id" type="input" class="col-7 form-control">
            <option label="Choisir le propriétaire..."></option>
          @foreach($users as $user)
            <option value='{{ $user->id }}'>{{ $user->identity() }}</option>
          @endforeach
        </select>
    </div>
  @error('app.auth_type')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-authtype">Authentification</label>
        <select id="app-authtype" wire:model="app.auth_type" type="input" class="col-7 form-control">
            <option label="Choisir le type d'authentification..."></option>
          @foreach(AP::getAppAuthTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
  @error('app.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-name">Nom</label>
        <input id="app-name" wire:model="app.name" type="input"
               class="col-7 form-control" placeholder="Nom de l'application">
    </div>
  @error('app.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-description">Description</label>
        <textarea id="app-description" wire:model="app.description" type="text" rows="3"
                  class="col-7 form-control" placeholder="Description de l'application">
        </textarea>
    </div>
  @error('app.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-icon">Icône</label>
        <input id="app-icon" wire:model="app.icon" type="input"
               class="col-7 form-control" placeholder="Icône (Material Design)">
    </div>
  @error('app.url')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-4'])
  @enderror
    <div class="form-group row">
        <label class="col-4 text-right my-auto" for="app-url">Url</label>
        <input id="app-url" wire:model="app.url" type="input"
               class="col-7 form-control" placeholder="Url d'accès à l'application">
    </div>
</div>
