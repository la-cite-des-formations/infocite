<!-- filtre ... -->
<div>
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Authentification">
                <span class="material-icons md-18">vpn_key</span>
            </div>
        </div>
        <select wire:model='filter.authType' class="form-control" id="app-type-filter">
            <option label="Choisir le type d'authentification..."></option>
          @foreach (AP::getAppAuthTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
</div>
