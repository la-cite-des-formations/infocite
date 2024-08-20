<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <!--Message flash-->
    @if (session()->has('error_alert'))
        <div id="errorAlert" class="alert alert-danger position-fixed start-50 translate-middle-x mt-2 w-50 p-2"
             style="z-index: 9999;opacity: 0.8;">
            <p class="text-center pt-1">{{ session('error_alert') }}</p>
        </div>
    @endif

    <!--Section notification, bouton mode édition, filtre et afichage-->
    <section id="posts" class="services section-bg">
        <div class="container d-flex justify-content-between mb-3">
            <div class="btn-group" role="group">
                <!--Bouton affichage liste/grille-->
                <button class="d-flex align-items-center btn @if(session('displayPosts') === 'grid') btn-primary @else btn-secondary @endif" title="Grille"
                        wire:click="displayGridPosts()">
                    <span class="bx bxs-grid-alt me-1" ></span>
                    Grille
                </button>
                <button class="d-flex align-items-center btn @if(session('displayPosts') === 'list') btn-primary @else btn-secondary @endif" title="Liste"
                        wire:click="displayListPosts()">
                    <span class="bx bx-list-ul me-1" ></span>
                    Liste
                </button>
            </div>
            <div class="btn-group" role="group">
                <button class="d-flex align-items-center btn btn-sm @if($notifications->count() == 0) btn-secondary @else btn-danger @endif"
                        wire:click="showModal('notify')" type="button" title="voir les notifications">
                  @if ($notifications->count() > 0)
                    <span class="me-1">{{ $notifications->count() }}</span>
                  @endif
                    <span class="bi bi-bell"></span>
                </button>
              @can('edit', ['App\\Post', $rubric->id])
                <button class="d-flex align-items-center btn btn-sm btn-primary" wire:click='switchMode' type="button"
                        title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                    <span class="bx @if ($mode == 'view') bx-pencil @else bx-show @endif"></span>
                </button>
              @endcan
              @if ($mode == 'edition')
               @can('create', ['App\\Post', $rubric->id])
                <a href="{{ route('post.create', ['rubric' => $rubric->route()]) }}"
                    title="Commencer un nouvel article"
                    type="button" class="d-flex align-items-center input-group-text btn btn-sm btn-success">
                    <span class="material-icons fs-5">add</span>
                </a>
               @endcan
              @endif
              @if($rubric->name != 'Une')
                <button class="d-flex align-items-center btn @if ($isFavoriteRubric) btn-warning @else btn-secondary @endif btn-sm"
                        title="@if ($isFavoriteRubric) Retirer des favoris @else Ajouter aux favoris @endif"
                        wire:click="switchFavoriteRubric" type="button">
                    <span class="bx bx-star"></span>
                </button>
              @else
                <!--Bouton filtre-->
                <button wire:click='toggleFilter()'
                        @class([
                            "d-flex align-items-center",
                            "btn btn-sm",
                            "btn-secondary" => $filter['allPosts'] == 'on',
                            "btn-success" => is_null($filter['allPosts'])
                        ])
                        title="{{ $showFilter ? 'Masquer' : 'Afficher' }} le filtre">
                    <span class="material-icons fs-5">filter_list</span>
                </button>
              @endif
            </div>
        </div>
      @if($showFilter)
        <div id="filterContainer" >
            @include('livewire.usage.search-manager', ['filter' => $filter, 'sorter' => $sorter])
        </div>
      @endif
        {{--Titre de la rubric--}}
        <div class="container">
            <div class="section-title">
                <div class="row justify-content-center">
                    <h2 class="col-9">{{ $rubric->title }}</h2>
                </div>
                <div class="container d-flex justify-content-center">
                    <!--Personnalisation de la description de la rubric en fonction du filtre actif-->
                    <div class="d-flex align-items-center">
                      @if($rubric->name === 'Une')
                       @if(Session::get('lastFilter'))
                        <span class="material-icons fs-2 me-1">{{ AP::getUneFilteredByName(Session::get('lastFilter'))['icone'] }}</span>
                        <p class="m-auto">{{ AP::getUneFilteredByName(Session::get('lastFilter'))['libelle'] }}</p>
                       @elseif(Session::get('lastSorter'))
                        <span class="material-icons fs-2 me-1">{{ AP::getUneSortedByName(Session::get('lastSorter'))['icone'] }}</span>
                        <p>{{ AP::getUneSortedByName(Session::get('lastSorter'))['libelle'] }}</p>
                       @endif
                      @endif
                    </div>
                </div>
            </div>
          @if(session('displayPosts')==='list' && !empty($posts->all()))
            <!-- Affichage des articles en liste -->
            <table class="posts-list w-100 mb-3" >
                <thead>
                    <tr>
                        <th class="col-6 ps-5">Article</th>
                        <th class="col-2">Rubrique</th>
                        <th class="col text-center">Maj</th>
                        <th class="col">Infos</th>
                        <th class="col">Options</th>
                    </tr>
                </thead>
                <tbody >
                  @if($rubric->name === 'Une' && Session::get('lastFilter') === 'allPosts')
                    @include('livewire.usage.posts-list', ['posts' => $pinnedPost, 'withPinning' => TRUE])
                    <tr height="@if ($pinnedPost->isNotEmpty()) 30px @else 10px @endif"></tr>
                  @else
                    <tr height="10px"></tr>
                  @endif
                    @include('livewire.usage.posts-list', ['posts' => $posts, 'withPinning' => FALSE])
                </tbody>
            </table>
          @else
            <!--Affichage des articles en carte-->
           @if($rubric->name === 'Une' && Session::get('lastFilter') === 'allPosts')
            <!-- Affichage des articles épinglés uniquement sur la "Une" et uniquement si le filtre "Tout les posts" est actif-->
            @include('livewire.usage.posts-pinnedPosts')
           @endif
            <!-- Affichage des autres articles-->
            <div class="row">
              @foreach ($posts as $i => $post)
               @can('read', $post)
                <div wire:key='{{$post->id}}' wire:click='redirectToPost({{ $post->id }})' role="button"
                    class="col-sm-12 col-md-4 col-lg-2 d-flex align-items-stretch mt-2 mb-3"
                    @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 6 + 1) * 100 }}" @endif>
                    <div class="position-relative icon-box d-flex flex-column">
                      @if (!$post->released && is_object($post->status))
                        <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger"
                            title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                      @endif
                        <!-- Titre de l'article et icone-->
                        <h4>
                            <div class="icon">
                                <i class="material-icons">{{ $post->icon }}</i>
                            </div>
                            <a>{{ $post->title }}</a>
                        </h4>
                        <!-- Sous Titre de l'article -->
                        <p>{!! $post->preview() !!}</p>
                        <!-- Boutons d'actions -->
                        <div wire:click.prefetch='blockRedirection'
                            class="position-relative align-self-end mt-auto">
                            <div class="input-group " role="group" aria-label="Actions">
                                <!-- Article publié ou non (pas un bouton d'action) -->
                              @if ($mode == 'edition')
                               @can('update', $post)
                                <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                    title="Modifier" role="button" class="btn btn-sm btn-success">
                                    <i class="bx bx-pencil"></i>
                                </a>
                               @endcan
                              @endif
                              @can('viewAny', ['App\\Comment', $post->id])
                                <!-- NB de commentaires déposés sur l'article : class primary si au moins 1 commentaire  -->
                                <div @class([
                                    'input-group-text btn-sm',
                                    'btn-primary' => $post->comments->isNotEmpty(),
                                    'btn-secondary' => $post->comments->isEmpty()
                                  ]) type="text" title="Commentaires">
                                    {{ $post->comments->count() ?: '' }}
                                    <i @class([
                                        "bx bx-comment-detail",
                                        "ms-1" => $post->comments->isNotEmpty(),
                                      ])></i>
                                </div>
                              @endcan
                                <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                <button
                                    class="btn @if ($post->isFavorite()) btn-warning @else btn-secondary @endif btn-sm"
                                    title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                                    wire:click="switchFavoritePost({{ $post->id }})"
                                    type="button">
                                    <i class="bx bx-star"></i>
                                </button>
                                <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                                @can('pin')
                                    <button
                                        class="btn @if ($post->is_pinned) btn-success @else btn-secondary @endif btn-sm"
                                        title="@if ($post->is_pinned) Désépingler l'article @else épingler l'article @endif"
                                        wire:click="switchPinnedPost({{ $post->id }})" type="button">
                                        <i class='bx bx-pin'></i>
                                    </button>
                                @endcan
                                <!-- Article deja lu ? : class success si deja lu -->
                                <div
                                    class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                                    type="text" @if ($post->isRead()) title="Déjà consulté"
                                    @else title="À consulter" @endif>
                                    <i class="bx bx-message-alt-check"></i>
                                </div>
                                @if ($mode == 'edition')
                                    @can('delete', $post)
                                        <button
                                            wire:click="showModal('confirm', {handling : 'deletePostFromRubric', postId : {{ $post->id }}})"
                                            type="button" class="btn btn-sm btn-danger" title="Supprimer">
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
          @endif
            @include('includes.pagination', ['elements' => $posts])
        </div>
    </section>

</div>
