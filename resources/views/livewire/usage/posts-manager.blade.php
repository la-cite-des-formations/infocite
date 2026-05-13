<section wire:key='rubricPostsList' id="posts" class="services section-bg">

    @if (session()->has('error_alert'))
        <!--Message flash-->
        <div class="alert alert-danger alert-dismissible position-fixed top-0 start-50 translate-middle-x w-50"
            id="errorAlert" style="z-index: 9999; opacity: 0.8; margin-top: 6rem">
            <div class="text-center">{{ session('error_alert') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container d-flex justify-content-center align-items-start position-relative">
        <div class="btn-group position-absolute start-0" role="group" style="z-index: 2;">
            <!--Bouton affichage liste/grille-->
            <button @class([
                'd-flex align-items-center',
                'btn btn-sm',
                'btn-primary' => session('displayPosts') === 'grid',
                'btn-secondary' => session('displayPosts') !== 'grid',
            ]) title="Grille" wire:click="displayGridPosts()">
                <span class="bx bxs-grid-alt me-1"></span>
                Grille
            </button>
            <button @class([
                'd-flex align-items-center',
                'btn btn-sm',
                'btn-primary' => session('displayPosts') === 'list',
                'btn-secondary' => session('displayPosts') !== 'list',
            ]) title="Liste" wire:click="displayListPosts()">
                <span class="bx bx-list-ul me-1"></span>
                Liste
            </button>
        </div>

        <div class="section-title w-100" style="padding-left: 300px; padding-right: 300px;">
            <h2 class="text-center mb-2">{{ $rubric->title }}</h2>
        </div>

        <div class="btn-group position-absolute end-0" role="group" style="z-index: 2;">
            <button @class([
                'd-flex align-items-center',
                'btn btn-sm',
                'btn-secondary' => $notifications->isEmpty(),
                'btn-danger' => $notifications->isNotEmpty(),
            ])
                wire:click="$emitTo('modal-manager', 'show', {component: 'usage.notifications-manager'})" type="button"
                title="voir les notifications">
                @if ($notifications->count() > 0)
                    <span class="me-1">{{ $notifications->count() }}</span>
                @endif
                <span class="bi bi-bell"></span>
            </button>
            @can('edit', ['App\\Models\\Post', $rubric->id])
                <button class="d-flex align-items-center btn btn-sm btn-primary" wire:click='switchMode' type="button"
                    title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                    <span @class([
                        'bx',
                        'bx-pencil' => $mode == 'view',
                        'bx-show' => $mode != 'view',
                    ])></span>
                </button>
            @endcan
            @if ($mode == 'edition')
                @can('create', ['App\\Models\\Post', $rubric->id])
                    <button class="d-flex align-items-center btn btn-sm btn-info text-white"
                        wire:click="$emitTo('modal-manager', 'show', {component: 'usage.post-templates-manager', data: {rubricId: {{ $rubric->id }}}})"
                        title="Gérer les modèles d'articles">
                        <span class="material-icons fs-5">history_edu</span>
                    </button>
                    <a href="{{ route('post.create', ['rubric' => $rubric->segmentPath()]) }}"
                        title="Commencer un nouvel article" type="button"
                        class="d-flex align-items-center input-group-text btn btn-sm btn-success">
                        <span class="material-icons fs-5">add</span>
                    </a>
                @endcan
            @endif

            @if (!in_array($rubric->name, ['Une', 'Archives']))
                <button @class([
                    'd-flex align-items-center',
                    'btn btn-sm',
                    'btn-warning' => $isFavoriteRubric,
                    'btn-secondary' => !$isFavoriteRubric,
                ])
                    title="@if ($isFavoriteRubric) Retirer des favoris @else Ajouter aux favoris @endif"
                    wire:click="switchFavoriteRubric" type="button">
                    <span class="bx bx-star"></span>
                </button>
                <button class="d-flex align-items-center btn btn-sm btn-primary"
                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.rubric-info', data: {rubricId : {{ $rubric->id }}}})"
                    type="button" title="Voir les droits de la rubrique">
                    <span class="material-icons fs-5">info</span>
                </button>
            @elseif($rubric->name == 'Une')
                <!--Bouton filtre-->
                <button wire:click='toggleFilter()' @class([
                    'd-flex align-items-center',
                    'btn btn-sm',
                    'btn-secondary' => $filter['allPosts'] == 'on',
                    'btn-success' => $filter['allPosts'] != 'on',
                ])
                    title="{{ $showFilter ? 'Masquer' : 'Afficher' }} le filtre">
                    <span class="material-icons fs-5">filter_list</span>
                </button>
            @endif
        </div>
    </div>

    @if ($showFilter)
        <div id="filterContainer">
            @include('livewire.usage.posts-sort-manager', ['filter' => $filter, 'sorter' => $sorter])
        </div>
    @endif

    <div class="container mt-1">
        @if (session('displayPosts') === 'list' && $posts->isNotEmpty())
            @if ($rubric->name === 'Une' && Session::get('lastFilter') === 'allPosts')
                <!-- Zone Hero en liste : Épinglés + Récents -->
                <table class="posts-list w-100 mb-3">
                    <colgroup>
                        <col class="col-6">
                        <col class="col-2">
                        <col class="col">
                        <col class="col">
                        <col class="col">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-6 ps-5">Article</th>
                            <th class="col-2">Rubrique</th>
                            <th class="col text-center">Maj</th>
                            <th class="col">Infos</th>
                            <th class="col">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('livewire.usage.posts-list', [
                            'posts' => $pinnedPost,
                            'withPinning' => true,
                        ])
                        <tr height="@if ($pinnedPost->isNotEmpty()) 30px @else 10px @endif"></tr>

                        @include('livewire.usage.posts-list', [
                            'posts' => $recentPosts,
                            'withPinning' => false,
                            'isRecent' => true,
                        ])
                        <tr height="20px"></tr>
                    </tbody>
                </table>
            @endif

            @if ($rubric->name === 'Une')
                @include('livewire.usage.posts-filter-badge')
            @endif

            <!-- Affichage des articles paginés en liste -->
            <table class="posts-list w-100 mb-3">
                <colgroup>
                    <col class="col-6">
                    <col class="col-2">
                    <col class="col">
                    <col class="col">
                    <col class="col">
                </colgroup>
                @if (!($rubric->name === 'Une' && Session::get('lastFilter') === 'allPosts'))
                    <thead>
                        <tr>
                            <th class="col-6 ps-5">Article</th>
                            <th class="col-2">Rubrique</th>
                            <th class="col text-center">Maj</th>
                            <th class="col">Infos</th>
                            <th class="col">Options</th>
                        </tr>
                    </thead>
                @endif
                <tbody>
                    <tr height="10px"></tr>
                    @include('livewire.usage.posts-list', ['posts' => $posts, 'withPinning' => false])
                </tbody>
            </table>
        @else
            <!--Affichage des articles en carte-->
            @if ($rubric->name === 'Une' && $filter['allPosts'] == 'on')
                <!-- Zone Hero Hybride : Épinglés (Gauche) / Récents (Droite) -->
                <div class="row mb-4">
                    <div class="col-12 col-lg-6">

                        @include('livewire.usage.posts-pinnedPosts')
                    </div>
                    <div class="col-12 col-lg-6">

                        @include('livewire.usage.posts-recentPosts')
                    </div>
                </div>
            @endif

            @if ($rubric->name === 'Une')
                @include('livewire.usage.posts-filter-badge')
            @endif

            <!-- Affichage des autres articles-->
            <div class="row">
                @foreach ($posts as $i => $post)
                    @can('read', $post)
                        <div wire:key='{{ $post->id }}' wire:click='redirectToPost({{ $post->id }})' role="button"
                            class="col-sm-12 col-md-4 col-lg-2 d-flex align-items-stretch mt-2 mb-3"
                            @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ (($i % 6) + 1) * 100 }}" @endif>
                            <div class="position-relative post-box icon-box d-flex flex-column">
                                @if (!$post->released && is_object($post->status))
                                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger"
                                        title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                                @endif
                                <!-- Titre de l'article et icone-->
                                <h4 class="mb-1 mt-1">
                                    <i class="material-icons ms-0 me-1 align-middle"
                                        style="color: var(--select-color-1); font-size: 30px;">{{ $post->icon }}</i>
                                    <a>{{ $post->title }}</a>
                                </h4>
                                <!-- Sous Titre de l'article -->
                                <p class="small mb-1">{!! AP::strLimiter(strip_tags($post->content), 80) !!}</p>
                                <!-- Boutons d'actions -->
                                <div wire:click.prefetch='blockRedirection'
                                    class="actions position-absolute bottom-0 end-0 m-2 z-index-2">
                                    <div class="list-group list-group-horizontal btn-group btn-group-sm" role="group"
                                        aria-label="Actions">
                                        <!-- Article publié ou non (pas un bouton d'action) -->
                                        @if ($mode == 'edition')
                                            @can('update', $post)
                                                <a href="{{ route('post.edit', ['rubric' => $post->rubric->segmentPath(), 'post_id' => $post->id]) }}"
                                                    title="Modifier" role="button"
                                                    class="btn btn-success small-action-btn d-flex">
                                                    <i class="bx bx-pencil my-auto"></i>
                                                </a>
                                            @endcan
                                        @endif
                                        @can('viewAny', ['App\\Models\\Comment', $post->id])
                                            <!-- NB de commentaires déposés sur l'article : class primary si au moins 1 commentaire  -->
                                            <button @class([
                                                'list-group-item small-action-btn',
                                                'list-group-item-primary' => $post->comments->isNotEmpty(),
                                                'list-group-item-secondary' => $post->comments->isEmpty(),
                                            ]) type="text"
                                                title="{{ $post->commentsInfo() }}">
                                                {{ $post->comments->count() ? $post->comments->count() . ' ' : '' }}
                                                <i class="bx bx-comment-detail"></i>
                                            </button>
                                        @endcan
                                        <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                        <button @class([
                                            'btn small-action-btn',
                                            'btn-warning' => $post->isFavorite,
                                            'btn-secondary' => !$post->isFavorite,
                                        ])
                                            title="@if ($post->isFavorite) Retirer des favoris @else Ajouter aux favoris @endif"
                                            wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                            <i class="bx bx-star"></i>
                                        </button>
                                        <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                                        @can('pin', $post)
                                            <button @class([
                                                'btn small-action-btn',
                                                'btn-success' => $post->is_pinned,
                                                'btn-secondary' => !$post->is_pinned,
                                            ])
                                                title="@if ($post->is_pinned) Désépingler l'article @else épingler l'article @endif"
                                                wire:click="switchPinnedPost({{ $post->id }})" type="button">
                                                <i class='bx bx-pin'></i>
                                            </button>
                                        @endcan
                                        <!-- Article deja lu ? : class success si deja lu -->
                                        <button @class([
                                            'list-group-item small-action-btn',
                                            'list-group-item-success' => $post->isRead(),
                                            'list-group-item-danger' => !$post->isRead(),
                                        ]) type="text"
                                            @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                                            <i class="bx bx-message-alt-check"></i>
                                        </button>
                                        @if ($mode == 'edition')
                                            @can('delete', $post)
                                                <button
                                                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling : 'deletePostFromRubric', postId : {{ $post->id }}}})"
                                                    type="button" class="btn btn-danger small-action-btn" title="Supprimer">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endforeach
            </div>
            @if ($posts->isEmpty())
                <div class="container d-flex justify-content-center">
                    <div class="col-sm-12 col-md-8 col-lg-6 alert alert-info text-center shadow-sm">
                        <i class="bx bx-info-circle fs-4 me-2"></i>
                        Aucun article à afficher pour cette rubrique ou avec ces filtres.
                    </div>
                </div>
            @endif
        @endif
        <!-- Pagination -->
        @include('includes.pagination', ['elements' => $posts, 'perPage' => 'perPage'])

        @if ($mode == 'edition' && $rubric->name != 'Archives' && $templates->isNotEmpty())
            <div class="mt-5 pt-4 border-top">
                <div class="d-inline-flex align-items-center link-info mb-3" role="button"
                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.post-templates-manager', data: {rubricId: {{ $rubric->id }}}})"
                    title="Gérer les modèles d'articles">
                    <span class="material-icons me-2 ms-0">history_edu</span>
                    <h3 class="h4 mb-0 fw-bold">Modèles disponibles</h3>
                </div>
                <div class="row">
                    @foreach ($templates as $template)
                        <div class="col-sm-6 col-md-4 col-lg-2 mb-3">
                            <div class="card h-100 border-info border-opacity-25 shadow-sm template-card"
                                style="background-color: #f0f7ff; transition: all 0.3s ease-in-out;">
                                <div class="card-body p-3 d-flex flex-column">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="material-icons text-primary me-2">{{ $template->icon }}</i>
                                        <h5 class="card-title mb-0 fs-6 fw-bold text-dark">{{ $template->title }}</h5>
                                    </div>
                                    <div class="mt-auto pt-3 text-center">
                                        <div class="btn-group btn-group-sm w-100 shadow-sm" role="group">
                                            <a href="{{ route('post.create', ['rubric' => $rubric->segmentPath(), 'from_template' => $template->id]) }}"
                                                class="btn btn-outline-primary bg-white small-action-btn d-flex justify-content-center align-items-center w-25"
                                                title="Créer un article à partir de ce modèle">
                                                <i class='bx bx-plus-circle'></i>
                                            </a>
                                            <a href="{{ route('post.edit', ['rubric' => $template->rubric ? $template->rubric->segmentPath() : ($rubric->name == 'Une' ? 'une' : $rubric->segmentPath()), 'post_id' => $template->id]) }}"
                                                class="btn btn-outline-success bg-white small-action-btn d-flex justify-content-center align-items-center w-25"
                                                title="Modifier le modèle">
                                                <i class='bx bx-pencil'></i>
                                            </a>
                                            <button
                                                wire:click="$emitTo('modal-manager', 'show', {component: 'usage.duplicate-template-manager', data: {templateId: {{ $template->id }}}})"
                                                type="button"
                                                class="btn btn-outline-warning bg-white small-action-btn d-flex justify-content-center align-items-center w-25"
                                                title="Dupliquer le modèle">
                                                <i class='bx bx-copy'></i>
                                            </button>
                                            <button
                                                wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling : 'deletePostFromRubric', postId : {{ $template->id }}, isTemplate : true}})"
                                                type="button"
                                                class="btn btn-outline-danger bg-white small-action-btn d-flex justify-content-center align-items-center w-25"
                                                title="Supprimer le modèle">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <style>
                .template-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                }

                .template-card .btn-outline-primary:hover {
                    background-color: var(--bs-primary) !important;
                    color: white !important;
                }

                .template-card .btn-outline-success:hover {
                    background-color: var(--bs-success) !important;
                    color: white !important;
                }

                .template-card .btn-outline-warning:hover {
                    background-color: var(--bs-warning) !important;
                    color: white !important;
                }

                .template-card .btn-outline-danger:hover {
                    background-color: var(--bs-danger) !important;
                    color: white !important;
                }

                .w-25 {
                    width: 25%;
                }
            </style>
        @endif
    </div>
</section>
