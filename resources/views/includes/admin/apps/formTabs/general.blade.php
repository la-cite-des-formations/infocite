<div class="col pt-2">
 @can('createFor', $app)
  @error('app.id_owner')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-owner">Propriétaire</label>
        <div class="col-6">
            <select id="app-owner" wire:model="app.owner_id" type="input" class="form-select">
                <option label="Choisir le propriétaire..."></option>
              @foreach($users as $user)
                <option value='{{ $user->id }}'>{{ $user->identity() }}</option>
              @endforeach
            </select>
        </div>
    </div>
 @endcan
  @error('app.auth_type')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-authtype">Authentification</label>
        <div class="col-6">
            <select id="app-authtype" wire:model="app.auth_type" type="input" class="form-select">
                <option label="Choisir le type d'authentification..."></option>
              @foreach(AP::getAppAuthTypes() as $typeKey => $typeName)
                <option value='{{ $typeKey }}'>{{ $typeName }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('app.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-name">Nom</label>
        <div class="col-6">
            <input id="app-name" wire:model="app.name" type="input"
               class="form-control" placeholder="Nom de l'application">
        </div>
    </div>
  @error('app.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-description">Description</label>
        <div class="col-6">
            <textarea id="app-description" wire:model="app.description" type="text" rows="3"
                  class="form-control" placeholder="Description de l'application">
            </textarea>
        </div>
    </div>
  @error('app.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-icon">Icône</label>
        @include('includes.icon-picker', ['model' => 'app', 'inAdminInterface' => TRUE])
    </div>
  @error('app.url')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="app-url">Url</label>
        <div class="col-6">
            <input id="app-url" wire:model="app.url" type="input"
               class="form-control" placeholder="Url d'accès à l'application">
        </div>
    </div>
</div>
