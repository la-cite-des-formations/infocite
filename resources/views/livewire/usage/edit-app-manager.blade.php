<div wire:key='appEdit' id="lw-edit-app">
    <section id="breadcrumbs" class="breadcrumbs">
    </section>

    <section id="edit-app" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon">
                <i class="material-icons-outlined md-36">extension</i>
                Applications personnelles - {{ $mode === 'edition' ? 'Édition' : 'Ajout' }}
            </h2>
            <p>
                Ce formulaire vous permet {{ $mode === 'edition' ? 'de modifier' : "d'ajouter" }} une application personnelle accessible depuis l'intranet
            </p>
        </div>
        <form id="appForm">
            @includeWhen(session()->has('message'), 'includes.confirm-message')
          @error('app.name')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="app-name">Nom</label>
                <div class="col-8">
                    <input id="app-name" wire:model.defer="app.name" type="input"
                           class="form-control" placeholder="Nom de l'application">
                </div>
            </div>
          @error('app.description')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="app-description">Descriptif</label>
                <div class="col-8">
                    <textarea id="app-description" wire:model.defer="app.description" type="input"
                              class="form-control" placeholder="Descriptif de l'application">
                    </textarea>
                </div>
            </div>
          @error('app.icon')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="app-icon">Icône</label>
                <div class="col-8">
                    @include('includes.icon-picker', ['model' => 'app'])
                </div>
            </div>
          @error('app.url')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="app-url">Url</label>
                <div class="col-8">
                    <input id="app-url" wire:model.defer="app.url" type="input"
                           class="form-control" placeholder="Url d'accès à l'application">
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-end">
                    <a href="{{ $backRoute }}" type="button" class="btn btn-secondary me-1" title="Revenir à la page précédente sans enregistrer">
                        {{ $mode === 'edition' ? 'Fermer' : 'Annuler' }}
                    </a>
                    @if ($mode === 'edition')
                    <button wire:click="showModal('confirm', {handling : 'update'})" type="button" class="btn btn-primary me-1" title="Enregistrer les modifications">
                        Modifier
                    </button>
                    @else
                    <button wire:click="showModal('confirm', {handling : 'create'})" type="button" class="btn btn-primary me-1" title="Enregistrer les modifications">
                        Créer
                    </button>
                    @endif
                  @if ($mode === 'edition')
                    <a href="{{ $rubricRoute.'/personal-apps/create' }}" title="Ajouter une nouvelle application personnelle"
                            type="button" class="d-flex btn btn-sm btn-success me-1">
                        <span class="material-icons">add</span>
                    </a>
                  @endif
                </div>
            </div>
        </form>
    </section>
</div>
