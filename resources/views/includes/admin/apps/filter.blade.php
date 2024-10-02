<!-- filtre ... -->
<div>
  @can('filterByType', 'App\\App')
    <div class="input-group">
        <span class="input-group-text text-secondary material-icons md-18" title="Type d'applications">category</span>
        <select wire:model='filter.type' class="form-select" id="app-type-filter">
            <option label="Choisir le type d'applications..."></option>
          @foreach (AP::getAppTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
  @endcan
    <div class="input-group mt-1">
        <span class="input-group-text text-secondary material-icons md-18" title="Authentification">vpn_key</span>
        <select wire:model='filter.authType' class="form-select" id="app-authtype-filter">
            <option label="Choisir le type d'authentification..."></option>
          @foreach (AP::getAppAuthTypes() as $authtypeKey => $authtypeName)
            <option value='{{ $authtypeKey }}'>{{ $authtypeName }}</option>
          @endforeach
        </select>
    </div>
</div>
