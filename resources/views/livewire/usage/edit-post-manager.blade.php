<div wire:key='postEdit' id="lw-edit-post">
    <section id="breadcrumbs" class="breadcrumbs">
    </section>

    <section id="edit-post" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon">
                <i class="material-icons-outlined md-36">history_edu</i>
                Contenu - {{ $mode === 'edition' ? 'Édition' : 'Création' }}
            </h2>
            <p>
                Ce formulaire vous permet de {{ $mode === 'edition' ? 'modifier' : 'créer' }} un article consultable sur l'intranet
            </p>
        </div>
        <form id="postForm">
            @includeWhen(session()->has('message'), 'includes.confirm-message')
          @error('post.rubric_id')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="post-rubric-id">Rubrique</label>
                <div class="col-8">
                    <select id="post-rubric-id" wire:model="post.rubric_id" type="input" class="form-select">
                        <option label="Choisir la rubrique..."></option>
                      @foreach($rubrics as $rubric)
                       @can('create', ['App\\Post', $rubric->id])
                        <option value='{{ $rubric->id }}'>
                            {{ (is_object($rubric->parent) ? $rubric->parent->name.' / ' : '').$rubric->name }}
                        </option>
                       @endcan
                      @endforeach
                    </select>
                </div>
            </div>
          @error('post.title')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto" for="post-title">Titre</label>
                <div class="col-8">
                    <input id="post-title" wire:model="post.title" type="input"
                           class="form-control" placeholder="Titre de l'article">
                </div>
            </div>
          @error('post.icon')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto">Icône</label>
                <div class="col-8">
                    @include('includes.icon-picker', ['model' => 'post'])
                </div>
            </div>
          @error('post.content')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
          @enderror
            <div class="row mb-3">
                <label class="col-2 fw-bold text-end my-auto mt-1" for="post-content">Contenu</label>
                <div wire:ignore id="post-content-editor" class="col-8">
                    <textarea id="post-content" wire:model="post.content" type="input"
                              class="form-control tinymce" placeholder="Contenu de l'article">
                    </textarea>
                </div>
            </div>
         @can('block', ['App\\Comment'])
            <div class="row mb-3">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="form-check">
                        <input id="block-comments" wire:model="blockComments"
                               type="checkbox" class="form-check-input">
                        <label class="my-auto" for="block-comments">Bloquer les commentaires</label>
                    </div>
                </div>
            </div>
         @endcan
         @can('publish', ['App\\Post', $currentRubric->id])
           @error('post.published')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
           @enderror
            <div class="row mb-3">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="form-check">
                        <input id="post-published" wire:model="post.published"
                               type="checkbox" class="form-check-input">
                        <label class="my-auto" for="post-published">Publier</label>
                    </div>
                </div>
            </div>
          @if ($post->published)
           @error('post.published_at')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
           @enderror
            <div class="row mb-3">
                <label class="col-4 fw-bold text-end my-auto" for="post-published_at">Date de publication</label>
                <div class="col-2">
                    <input id="post-published_at" wire:model="post.published_at" type="date"
                        class="form-control" placeholder="Date de publication">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-4 fw-bold text-end my-auto" for="post-expired_at">Date d'expiration</label>
                <div class="col-2">
                    <input id="post-expired_at" wire:model="post.expired_at" type="date"
                        class="form-control" placeholder="Date d'expiration">
                </div>
            </div>
          @endif
         @endcan
            <div class="row">
                <div class="col-10 d-flex justify-content-end">
                    <a href="{{ $backRoute }}" type="button" class="btn btn-secondary me-1"
                       title="Revenir à la page précédente sans enregistrer">
                        {{ $mode === 'edition' ? 'Fermer' : 'Annuler' }}
                    </a>
                    @if ($mode === 'edition')
                    <button wire:click="showModal('confirm', {handling : 'update'})" type="button"
                            class="btn btn-primary me-1" title="Enregistrer les modifications">
                        Modifier
                    </button>
                    @else
                    <button wire:click="showModal('confirm', {handling : 'create'})" type="button"
                            class="btn btn-primary me-1" title="Enregistrer les modifications">
                        Créer
                    </button>
                    @endif
                  @if ($mode === 'edition')
                   @can('create', ['App\\Post', $currentRubric->id])
                    <a href="{{ $currentRubric->route().'/create' }}" title="Commencer un nouvel article"
                       type="button" class="d-flex btn btn-sm btn-success me-1">
                        <span class="material-icons">add</span>
                    </a>
                   @endcan
                  @endif
                </div>
            </div>
        </form>
    </section>
</div>
