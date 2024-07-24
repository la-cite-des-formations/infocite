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
                Ce formulaire vous permet de {{ $mode === 'edition' ? 'modifier' : 'créer' }} un article consultable sur
                l'intranet
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
                @include('includes.icon-picker', ['model' => 'post'])
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
                            <label class="my-auto" for="post-published">Publier - <span
                                    class="fst-italic bg-warning text-danger px-1">Important ! Pensez à mettre à jour la date de publication pour faire remonter votre article plus en avant</span></label>
                        </div>
                    </div>
                </div>
                @if ($post->published)
                    @error('post.published_at')
                        @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
                    @enderror
                    <div class="row mb-3">
                        <label class="col-4 fw-bold text-end my-auto" for="post-published_at">Date de
                            publication</label>
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
                    <div class="row mb-0">
                        <div class="col-4"></div>
                        <div class="col-6">
                            <div class="form-check">
                                <input id="radioPostAutoDeleteFalse" name="radioPostAutoDelete" type="radio"
                                       class="form-check-input" value='0'
                                       wire:model='post.auto_delete' @if (!($post->expired_at)) disabled @endif>
                                <label class="form-check-label" for="radioPostAutoDeleteFalse">
                                    Archiver à l'expiration
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"></div>
                        <div class="col-6">
                            <div class="form-check">
                                <input id="radioPostAutoDeleteTrue" name="radioPostAutoDelete" type="radio"
                                       class="form-check-input" value='1'
                                       wire:model='post.auto_delete' @if (!($post->expired_at)) disabled @endif>
                                <label class="form-check-label" for="radioPostAutoDeleteTrue">
                                    Supprimer à l'expiration
                                </label>
                            </div>
                        </div>
                    </div>
                    @can('pin')
                        @error('post.pinPost')
                            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
                        @enderror
                        <div class="row mb-3 d-flex justify-content-center">
                            <div class="col-4"></div>
                            <div class="col-8">
                                <div class="form-check">
                                    <input id="pin-post" wire:model="pinPost"
                                           type="checkbox" class="form-check-input">
                                    <label class="my-auto" for="pin-post">Épingler l'article</label>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif
            @endcan
            <div class="row">
                <div class="col-10 d-flex justify-content-end">
                    <a href="{{ $backRoute }}" type="button" class="btn btn-secondary me-1"
                       title="Revenir à la page précédente sans enregistrer">
                        {{ $mode === 'edition' ? 'Fermer' : 'Annuler' }}
                    </a>
                    @if ($mode === 'edition')
                        <button
                            wire:click="showModal('confirm', {handling : 'update', redirectionRoute : 'post.index'})"
                            type="button"
                            class="btn btn-primary me-1" title="Enregistrer les modifications et Visualiser l'article">
                            Modifier et Voir
                        </button>
                        <button wire:click="showModal('confirm', {handling : 'update'})" type="button"
                                class="btn btn-primary me-1" title="Enregistrer les modifications">
                            Modifier
                        </button>
                    @else
                        <button
                            wire:click="showModal('confirm', {handling : 'create', redirectionRoute : 'post.index'})"
                            type="button"
                            class="btn btn-primary me-1" title="Créer et Visualiser l'article">
                            Créer et Voir
                        </button>
                        <button wire:click="showModal('confirm', {handling : 'create'})" type="button"
                                class="btn btn-primary me-1" title="Créer l'article">
                            Créer
                        </button>
                    @endif
                    @if ($mode === 'edition')
                        @can('create', ['App\\Post', $currentRubric->id])
                            <a href="{{ route('post.create', ['rubric' => $currentRubric->route(), 'backRoute' => $backRoute]) }}"
                               title="Commencer un nouvel article" type="button"
                               class="d-flex btn btn-sm btn-success me-1">
                                <span class="material-icons">add</span>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        </form>
    </section>
</div>
