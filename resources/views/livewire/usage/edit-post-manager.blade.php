<section wire:key='postEdit' id="lw-edit-post" class="services section-bg">
    <div class="section-title">
        <h2 class="title-icon">
            <i class="material-icons-outlined md-36">history_edu</i>
            {{ $post->is_template ? 'Modèle' : 'Contenu' }} - {{ $mode === 'edition' ? 'Édition' : 'Création' }}
        </h2>
        <p>Ce formulaire vous permet de {{ $mode === 'edition' ? 'modifier' : 'créer' }}
            {{ $post->is_template ? "un modèle d'article" : "un article consultable sur l'intranet" }}</p>
    </div>
    <form id="postForm">
        @includeWhen(session()->has('message'), 'includes.confirm-message')
        @error('post.rubric_id')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
        @enderror
        <input type="hidden" wire:model="post.is_template">
        @if (!$post->is_template && $templates->isNotEmpty())
            <div class="row mb-3">
                <div class="col-2"></div>
                <div class="col-8">
                    <button type="button"
                        onclick="@this.openApplyTemplateModal(window.tinymce && window.tinymce.activeEditor ? window.tinymce.activeEditor.getContent() : '')"
                        class="btn btn-outline-primary form-control">
                        <span class="material-icons align-middle me-2" style="font-size:18px">library_add</span>
                        Appliquer un modèle
                    </button>
                    <div class="form-text text-primary mt-1">
                        <i class="bx bx-info-circle"></i> Le modèle sera ajouté à la fin de votre saisie actuelle (avec
                        prévisualisation).
                    </div>
                </div>
            </div>
        @endif
        <div class="row mb-3">
            <label class="col-2 fw-bold text-end my-auto" for="post-rubric-id">Rubrique</label>
            <div class="col-8">
                <select id="post-rubric-id" wire:model="post.rubric_id" type="input" class="form-select">
                    <option label="Choisir la rubrique..."></option>
                    @foreach ($rubrics as $rubric)
                        @can('create', ['App\\Models\\Post', $rubric->id])
                            <option value='{{ $rubric->id }}'>
                                {{ (is_object($rubric->parent) ? $rubric->parent->name . ' / ' : '') . $rubric->name }}
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
            <label class="col-2 fw-bold text-end my-auto">Icône</label>
            <div class="col-8 d-flex align-items-center">
                @include('includes.icon-picker', ['model' => 'post'])
                <label class="fw-bold ms-3 me-2 my-auto"
                    for="post-title">{{ $post->is_template ? 'Nom' : 'Titre' }}</label>
                <input id="post-title" wire:model="post.title" type="input" class="form-control flex-grow-1"
                    placeholder="{{ $post->is_template ? 'Nom du modèle' : 'Titre de l\'article' }}">
            </div>
        </div>
        @error('post.content')
            @include('includes.rules-error-message', ['labelsColLg' => 'col-2'])
        @enderror
        <div class="row mb-3">
            <label class="col-2 fw-bold text-end my-auto mt-1" for="post-content">Contenu</label>
            <div wire:ignore id="post-content-editor" class="col-8">
                <textarea id="post-content" wire:model="post.content" type="input" class="form-control tinymce"
                    placeholder="Saisir ici le contenu de l'article...">
                </textarea>
            </div>
        </div>
        @unless ($post->is_template)
            {{-- Galerie Photos --}}
            <div class="row mb-3">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bx bx-error-circle me-1"></i>
                        <strong>Note :</strong> Les modifications de la galerie (envoi, suppression, ordre) ne seront
                        définitivement enregistrées que lorsque vous <strong>sauvegarderez l'article</strong> (boutons en
                        bas de page).
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <livewire:usage.post-gallery :postId="$post->id" :wire:key="'gallery-' . ($post->id ?? 'new')" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-6">
                            @can('publish', ['App\\Models\\Post', $currentRubric->id])
                                <div class="form-check">
                                    <input id="post-published" wire:model="post.published" type="checkbox"
                                        class="form-check-input">
                                    <label class="fw-bold my-auto" for="post-published">Publier l'article</label>
                                </div>
                                <div class="mb-2">
                                    <span class="small fst-italic bg-warning text-danger px-1">
                                        Pour mettre en évidence votre article, la date de publication doit être à jour.
                                    </span>
                                </div>
                                @if ($post->published)
                                    <div class="mb-2">
                                        <label class="small fw-bold" for="post-published_at">Date de parution</label>
                                        <input id="post-published_at" wire:model="post.published_at" type="date"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold" for="post-expired_at">Date d'expiration</label>
                                        <input id="post-expired_at" wire:model="post.expired_at" type="date"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="ps-3">
                                        <div class="form-check">
                                            <input id="radioPostAutoDeleteFalse" name="radioPostAutoDelete" type="radio"
                                                class="form-check-input" value='0' wire:model='post.auto_delete'
                                                @if (!$post->expired_at) disabled @endif>
                                            <label class="form-check-label small" for="radioPostAutoDeleteFalse">Archiver à
                                                l'expiration</label>
                                        </div>
                                        <div class="form-check">
                                            <input id="radioPostAutoDeleteTrue" name="radioPostAutoDelete" type="radio"
                                                class="form-check-input" value='1' wire:model='post.auto_delete'
                                                @if (!$post->expired_at) disabled @endif>
                                            <label class="form-check-label small" for="radioPostAutoDeleteTrue">Supprimer à
                                                l'expiration</label>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light small py-1 border-0">
                                    <i class="bx bx-info-circle me-1"></i>Vous n'avez pas les droits de publication.
                                </div>
                            @endcan
                        </div>

                        <div class="col-6 ps-4">
                            <div class="fw-bold mb-2">Autres options</div>
                            @can('block', ['App\\Models\\Comment'])
                                <div class="form-check mb-2">
                                    <input id="block-comments" wire:model="blockComments" type="checkbox"
                                        class="form-check-input">
                                    <label class="my-auto" for="block-comments">Bloquer les commentaires</label>
                                </div>
                            @endcan
                            <div class="form-check mb-2">
                                <input id="post-acknowledgment" wire:model="post.is_acknowledgment_required"
                                    type="checkbox" class="form-check-input">
                                <label class="my-auto" for="post-acknowledgment">Lecture avec accusé de réception</label>
                            </div>
                            <div class="form-check mb-2">
                                <input id="post-rating" wire:model="post.is_rating_enabled" type="checkbox"
                                    class="form-check-input">
                                <label class="my-auto" for="post-rating">Activer la notation (système d'étoiles)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endunless
        <div class="row mt-5">
            <div class="col-10 d-flex justify-content-end">
                <a href="{{ $backRoute }}" type="button" class="btn btn-secondary me-1"
                    title="Revenir à la page précédente sans enregistrer">
                    Annuler
                </a>
                @if ($mode === 'edition')
                    @if (!$post->is_template)
                        <button
                            wire:click="showModal('confirm', {handling : 'update', redirectionRoute : 'post.index'})"
                            type="button" class="btn btn-primary me-1"
                            title="Enregistrer les modifications et Visualiser l'article">
                            Modifier et Voir
                        </button>
                    @endif
                    <button wire:click="showModal('confirm', {handling : 'update'})" type="button"
                        class="btn btn-primary me-1"
                        title="{{ $post->is_template ? 'Enregistrer le modèle' : 'Enregistrer les modifications' }}">
                        Modifier
                    </button>
                @else
                    @if (!$post->is_template)
                        <button
                            wire:click="showModal('confirm', {handling : 'create', redirectionRoute : 'post.index'})"
                            type="button" class="btn btn-primary me-1" title="Créer et Visualiser l'article">
                            Créer et Voir
                        </button>
                    @endif
                    <button wire:click="showModal('confirm', {handling : 'create'})" type="button"
                        class="btn btn-primary me-1"
                        title="{{ $post->is_template ? 'Créer le modèle' : 'Créer l\'article' }}">
                        Créer
                    </button>
                @endif
                @if ($mode === 'edition' && !$post->is_template)
                    <button type="button"
                        onclick="@this.openExtractTemplateModal(window.tinymce && window.tinymce.activeEditor ? window.tinymce.activeEditor.getContent() : '')"
                        class="btn btn-info text-white me-1" title="Créer un nouveau modèle à partir de cet article">
                        Modéliser
                    </button>
                @endif
                @if ($mode === 'edition')
                    @can('create', ['App\\Models\\Post', $currentRubric->id])
                        <a href="{{ route('post.create', ['rubric' => $currentRubric->route(), 'backRoute' => $backRoute] + ($post->is_template ? ['template' => 1] : [])) }}"
                            title="{{ $post->is_template ? 'Créer un nouveau modèle' : 'Commencer un nouvel article' }}"
                            type="button" class="d-flex btn btn-sm btn-success me-1">
                            <span class="material-icons">add</span>
                        </a>
                    @endcan
                @endif
            </div>
        </div>
    </form>
</section>
