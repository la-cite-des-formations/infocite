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
        <div wire:ignore.self>
                <button wire:ignore.self id="dropdownMenu2" data-toggle="dropdown" aria-expanded="false" type="button"
                        class="btn btn-light choice-icon-btn dropdown-toggle">
                    <span class="material-icons mr-1 mt-1">{{ (!($app->icon) ? '...' : $app->icon) }}</span>
                </button>
            <div wire:ignore.self class="dropdown-menu text-muted ml-1 p-1 border-0">
                <div class="input-group mb-1 border-0">
                    <div class="input-group-text text-secondary border-0">
                        <span class="material-icons">search</span>
                    </div>
                    <input wire:model='searchIcons' type="text" class="form-control search-icons border-0" placeholder="Rechercher...">
                </div>
                <div id="app-icon" wire:model="app.icon" type="input" style="max-width: 400px; max-height: 202px;"
                     class="d-flex flex-wrap overflow-auto justify-content-start p-0 border-0">
                  @foreach($icons as $miName => $miCode)
                    <button wire:click="choiceIcon('{{$miName}}', 'app')" type="button" value='{{ $miName }}' title='{{ $miName }}'
                            class='btn btn-sm {{ $app->icon === $miName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                        <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
                    </button>
                  @endforeach
                </div>
            </div>
        </div>
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
