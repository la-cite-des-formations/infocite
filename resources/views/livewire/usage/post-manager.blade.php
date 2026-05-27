<section wire:key='postView' id="post" class="services section-bg">

    <!--Message flash-->
    @if (session()->has('error_alert'))
        <div class="alert alert-danger alert-dismissible position-fixed top-0 start-50 translate-middle-x w-50"
            id="errorAlert" style="z-index: 9999; opacity: 0.8; margin-top: 6rem">
            <div class="text-center">{{ session('error_alert') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container d-flex justify-content-end">
        <div class="btn-group" role="group">
            <button @class([
                'btn btn-sm',
                'btn-danger' => $notifications->isNotEmpty(),
                'btn-secondary' => $notifications->isEmpty(),
            ]) wire:click="$emitTo('modal-manager', 'show', {component: 'usage.notifications-manager'})" type="button">
                @if ($notifications->isNotEmpty())
                    <span class="me-1">{{ $notifications->count() }}</span>
                @endif
                <i class="bi bi-bell"></i>
            </button>
            @can('edit', ['App\\Models\\Post', $post->rubric_id])
                <button class="btn btn-sm btn-primary" wire:click='switchMode' type="button"
                    title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                    <span class="bx @if ($mode == 'view') bx-pencil @else bx-show @endif"></span>
                </button>
            @endcan
            @if ($mode == 'edition')
                @can('create', ['App\\Models\\Post', $post->rubric_id])
                    <button class="d-flex align-items-center btn btn-sm btn-info text-white"
                        wire:click="$emitTo('modal-manager', 'show', {component: 'usage.post-templates-manager', data: {rubricId: {{ $post->rubric_id }}}})"
                        title="Gérer les modèles d'articles">
                        <span class="material-icons fs-5">history_edu</span>
                    </button>
                    <a href="{{ route('post.create', ['rubric' => $post->rubric->route()]) }}"
                        title="Commencer un nouvel article" type="button"
                        class="d-flex align-items-center input-group-text btn btn-sm btn-success">
                        <span class="material-icons fs-5">add</span>
                    </a>
                @endcan
            @endif
            @if ($post->rubric && !in_array($post->rubric->name, ['Une', 'Archives']))
                <button class="d-flex align-items-center btn btn-sm btn-primary"
                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.rubric-info', data: {rubricId : {{ $post->rubric_id }}}})"
                    type="button" title="Voir les droits de la rubrique parente de cet article">
                    <span class="material-icons fs-5">info</span>
                </button>
            @endif
        </div>
    </div>

    <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
        <div class="section-title">
            <div class="row justify-content-center">
                <h2 class="col-9 title-icon">
                    <i class="material-icons md-36 me-2">{{ $post->icon }}</i>{{ $post->title }}
                </h2>
            </div>
            <p>
                {{ $post->published ? 'Publié dans' : 'Non publié - ' }}
                <a
                    href="{{ route('rubric.index', ['rubric' => $post->rubric->route()]) }}">{{ $post->rubric->identity() }}</a>
            </p>
            <p class="fst-italic">Dernière mise à jours le {{ $post->updated_at->format('d/m/Y') }}</p>
        </div>

        @if ($post->is_acknowledgment_required && !$post->isAcknowledged())
            <div class="alert alert-warning alert-dismissible fade show mx-lg-5" role="alert">
                <i class="bx bx-error me-2"></i>
                <strong>Attention :</strong> Cet article nécessite un accusé de réception pour confirmer votre lecture.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mx-lg-5">
            <div class="card-header d-flex justify-content-end">
                <div id="post-actions">
                    <div class="input-group" role="group" aria-label="Actions">
                        @if ($mode == 'edition')
                            @can('update', $post)
                                <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                    role="button" class="btn btn-sm btn-success" title="Modifier">
                                    <i class="bx bx-pencil"></i>
                                </a>
                            @endcan
                            @can('delete', $post)
                                <button wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling : 'deletePost'}})" type="button"
                                    class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endcan
                        @endif
                        <button @class([
                            'btn btn-sm',
                            'btn-warning' => $post->isFavorite,
                            'btn-secondary' => !$post->isFavorite,
                        ])
                            title="{{ $post->isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                            wire:click="switchFavoritePost" type="button">
                            <i class="bx bx-star"></i>
                        </button>
                        @can('pin', $post)
                            <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                            <button @class([
                                'btn btn-sm',
                                'btn-success' => $post->is_pinned,
                                'btn-secondary' => !$post->is_pinned,
                            ]) wire:click="switchPinnedPost({{ $post->id }})"
                                type="button"
                                title="@if ($this->post->is_pinned) Désépingler l'article @else épingler l'article @endif">
                                <i class='bx bx-pin'></i>
                            </button>
                        @endcan
                        <div type="text" class="input-group-text btn-sm btn-primary">
                            Vu <span class="badge bg-light text-primary mx-1">{{ $post->readers->count() }}</span> fois
                        </div>
                        @if ($post->is_acknowledgment_required)
                            @can('edit', ['App\\Models\\Post', $post->rubric_id])
                                <button type="button" class="btn btn-sm btn-primary"
                                    title="Voir les utilisateurs ayant acquitté l'article"
                                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.post-acknowledgers', data: {id: {{ $post->id }}}})">
                                    Acquitté
                                    <span
                                        class="badge bg-light text-primary mx-1">{{ $post->acknowledgers->count() }}</span>
                                    fois
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="container">{!! $post->content !!}</div>

                {{-- Galerie Photos --}}
                @if ($post->gallery)
                    <div class="container">
                        @include('includes.gallery-grid', ['gallery' => $post->gallery])
                    </div>
                @endif

                @if ($post->is_acknowledgment_required)
                    <div class="d-flex justify-content-end align-items-center border-top-0 pt-0">
                        @if ($post->isAcknowledged())
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bx bx-check-circle me-1"></i>
                                Lu et approuvé le {{ $post->getAcknowledgment()->occurred_at->format('d/m/Y') }}
                            </span>
                        @else
                            <button wire:click="acknowledgeRead" wire:loading.attr="disabled" id="btn-acknowledge-read"
                                class="btn btn-outline-success px-4" title="Confirmer la lecture de cet article">
                                <span wire:loading.remove wire:target="acknowledgeRead">
                                    <i class="bx bx-check-shield me-1"></i>
                                    J'ai pris connaissance de ce document
                                </span>
                                <span wire:loading wire:target="acknowledgeRead">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Enregistrement...
                                </span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
            <div class="card-footer">
                @if ($post->is_rating_enabled)
                    <livewire:usage.post-rating :post="$post" :wire:key="'rating-' . $post->id" />
                @endif
                @can('viewAny', ['App\\Models\\Comment', $post->id])
                    <div class="text-muted">
                        <div class="col-lg-8 why-us mt-3">
                            <h5>{{ ($post->comments->count() ?: 'aucun') . ' commentaire' . ($post->comments->count() > 1 ? 's' : '') }}
                            </h5>
                            @can('create', ['App\\Models\\Comment', $post->id])
                                <div class="comment-form my-2">
                                    <input wire:model='newComment' wire:keydown.enter="commentPost" type="text"
                                        placeholder="Ajouter un commentaire">
                                    <button wire:click='commentPost' title="Ajouter">
                                        <i class="icofont-plus"></i>
                                    </button>
                                </div>
                            @endcan
                            <div class="container-fluid" @if ($firstLoad) data-aos="fade-up" @endif>
                                <div class="accordion-list px-0 pb-0">
                                    <ul>
                                        @foreach ($post->comments as $i => $comment)
                                            @can('view', $comment)
                                                <div class="d-flex">
                                                    <li class="mt-1 p-3 flex-fill">
                                                        <a data-bs-toggle="collapse" class="collapse"
                                                            data-bs-target="#accordion-list-{{ $i + 1 }}">
                                                            {{ $comment->author->identity() }} le
                                                            {{ $comment->created_at->format('d/m/Y') }}
                                                            <i class="bx bx-chevron-down icon-show"></i>
                                                            <i class="bx bx-chevron-up icon-close"></i>
                                                        </a>
                                                        <div id="accordion-list-{{ $i + 1 }}" class="collapse show">
                                                            <p>{!! preg_replace(
                                                                '/(http(s?):\/\/)(([[:punct:]]|[[:alnum:]]=?)*)/',
                                                                "<a href=\"\\0\">\\0</a> ",
                                                                trim($comment->content),
                                                            ) !!}</p>
                                                        </div>
                                                    </li>
                                                    @can('delete', $comment)
                                                        <button
                                                            wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling : 'deleteComment', id : {{ $comment->id }}}})"
                                                            title="Supprimer ce commentaire"
                                                            class="px-1 pb-0 ms-1 me-1 align-self-center btn btn-sm btn-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endcan
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>

</section>
