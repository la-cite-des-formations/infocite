<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    @if (session()->has('error_alert'))
        <div id="errorAlert" class="alert alert-danger position-fixed start-50 translate-middle-x mt-2 w-50 p-2"
             style="z-index: 9999;opacity: 0.8;">
            <p class="text-center pt-1">{{ session('error_alert') }}</p>
        </div>
    @endif

    <section id="posts" class="services section-bg">
        <div class="container d-flex flex-column">
            <div class="align-self-end">
                <div class="input-group" role="group">
                    <button class="btn btn-sm @if($notifications->count() == 0) btn-secondary @else btn-danger @endif"
                            wire:click="showModal('notify')" type="button">
                        @if ($notifications->count() > 0)
                            <span class="me-1">{{ $notifications->count() }}</span>
                        @endif
                        <i class="bi bi-bell"></i>
                    </button>
                    @can('edit', ['App\\Post', $rubric->id])
                        <button class="btn btn-sm btn-primary" wire:click='switchMode' type="button"
                                title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                            <span class="bx @if ($mode == 'view') bx-pencil @else bx-show @endif"></span>
                        </button>
                    @endcan
                    @if($rubric->name != 'Une')
                        <button class="btn @if ($isFavoriteRubric) btn-warning @else btn-secondary @endif btn-sm"
                                title="@if ($isFavoriteRubric) Retirer des favoris @else Ajouter aux favoris @endif"
                                wire:click="switchFavoriteRubric" type="button">
                            <i class="bx bx-star"></i>
                        </button>
                    @endif
                    @if ($mode == 'edition')
                        @can('create', ['App\\Post', $rubric->id])
                            <a href="{{ route('post.create', ['rubric' => $rubric->route()]) }}"
                               title="Commencer un nouvel article"
                               type="button" class="d-flex input-group-text btn btn-sm btn-success">
                                <span class="material-icons">add</span>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
        <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="d-flex flex-row">
                <div class="" style="background: var(--select-color-1); width: 100px">1</div>
                <div class="" style="background: var(--select-color-2); width: 100px">2</div>
                <div class="" style="background: var(--select-color-3); width: 100px">3</div>
                <div class="" style="background: var(--select-color-4); width: 100px">4</div>
                <div class="" style="background: var(--select-color-5); width: 100px">5</div>
                <div class="" style="background: var(--select-color-6); width: 100px">6</div>
                <div class="" style="background: var(--select-color-7); width: 100px">7</div>
                <div class="" style="background: var(--select-color-8); width: 100px">8</div>
                <div class="" style="background: var(--select-color-9); width: 100px">9</div>
                <div class="" style="background: var(--select-color-10); width: 100px">10</div>
                <div class="" style="background: var(--select-color-11); width: 100px">11</div>
                <div class="" style="background: var(--select-color-12); width: 100px">12</div>
                <div class="" style="background: var(--select-color-13); width: 100px">13</div>
                <div class="" style="background: var(--select-color-14); width: 100px">14</div>
                <div class="" style="background: var(--select-color-15); width: 100px">15</div>
                <div class="" style="background: var(--select-color-16); width: 100px">16</div>
                <div class="" style="background: var(--select-color-17); width: 100px">17</div>
                <div class="" style="background: var(--select-color-18); width: 100px">18</div>
                <div class="" style="background: var(--select-color-19); width: 100px">19</div>
                <div class="" style="background: var(--select-color-20); width: 100px">20</div>
                <div class="" style="background: var(--select-color-21); width: 100px">21</div>
                <div class="" style="background: var(--select-color-22); width: 100px">22</div>
            </div>
            <!-- Affichage des articles épinglés uniquement sur la "Une"-->


            @if($rubric->name === 'Une')
                <div class="section-title">
                    <div class="row justify-content-center">
                        <h2 class="col-9">Articles épinglés</h2>
                    </div>
                    <p>Articles à ne pas manquer... </p>
                </div>
                <div class="row">
                    @foreach ($pinnedPost as $i => $post)
                        @can('read', $post)
                            <div wire:click='redirectToPost({{ $post->id }})' role="button"
                                 class="pinnedPost col-sm-12 col-md-6 col-lg-3 d-flex align-items-stretch mt-2 mb-3"
                                 @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 4 + 1) * 100 }}" @endif>
                                <div class="pinnedPost position-relative icon-box d-flex flex-column">
                                    @if (!$post->released && is_object($post->status))
                                        <i class="position-absolute bottom-0 start-0 ms-2 mb-3 material-icons text-danger"
                                           title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                                    @endif
                                    <!-- Titre de l'article et icone-->
                                    <h4>
                                        <div class="icon">
                                            <a class=""><i
                                                    class="material-icons">{{ $post->icon }}</i>{!! $post->previewTitle() !!}
                                            </a>
                                        </div>
                                    </h4>

                                    <!-- Sous Titre de l'article -->
                                    <p>{!! $post->preview() !!}</p>

                                    <!-- Boutons d'actions -->
                                    <div wire:click.prefetch='blockRedirection' class="align-self-end mt-auto">
                                        <div class="input-group" role="group" aria-label="Actions">
                                            @if ($mode == 'edition')
                                                @can('update', $post)
                                                    <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                                       title="Modifier" role="button" class="btn btn-sm btn-success">
                                                        <i class="bx bx-pencil"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            @can('viewAny', ['App\\Comment', $post->id])
                                                <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                                                <div
                                                    class="input-group-text btn-sm @if ($post->comments->count() > 0) btn-primary @else btn-secondary @endif"
                                                    type="text" title="Commentaires">
                                                    @if ($post->comments->count() > 0)
                                                        <span class="me-1">{{ $post->comments->count() }}</span>
                                                    @endif
                                                    <i class="bx bx-comment-detail"></i>
                                                </div>
                                            @endcan
                                            <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                            <button
                                                class="btn @if ($post->isFavorite()) btn-warning @else btn-secondary @endif btn-sm"
                                                title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                                                wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                                <i class="bx bx-star"></i>
                                            </button>
                                            @can('pin')
                                                <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                                                <button
                                                    class="btn @if ($post->isPinned()) btn-success @else btn-secondary @endif btn-sm"
                                                    title="@if ($post->isPinned()) Désépingler l'article @else épingler l'article @endif"
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
        </div>
        {{--Articles à la Une--}}
        <section id="breadcrumbs" class="breadcrumbs my-4">
        </section>
        <!--Bouton de filtre, de trie et d'affichage-->
        <div class="container d-flex flex-column">
            @if($rubric->name === 'Une')
                <div class="align-self-start">
                    <div class="input-group" role="group">
                        <div class="input-group-append">
                            <button wire:click='toggleFilterMenu' class="btn btn-sm btn-secondary"
                                    title="{{ $showFilter ? 'Masquer' : 'Afficher'}} le filtre">
                                <span class="material-icons md-18">filter_list</span>
                            </button>
                        </div>
                    </div>
                    @if($showFilter)
                        @include('livewire.usage.search-manager',['filter'=>$filter, 'sorter'=>$sorter])
                    @endif
                </div>
            @endif
        </div>
        <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="section-title">
                <div class="row justify-content-center">
                    <h2 clas="col-9">{{ $rubric->title }}</h2>
                </div>
                <p>{{ $rubric->description }}</p>
            </div>
            <div class="row">
                @foreach ($posts as $i => $post)
                    @can('read', $post)
                        <div wire:click='redirectToPost({{ $post->id }})' role="button"
                             class="col-sm-12 col-md-6 col-lg-3 d-flex align-items-stretch mt-2 mb-3"
                             @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 4 + 1) * 100 }}" @endif>
                            <div class="position-relative icon-box d-flex flex-column">

                                <!-- Titre de l'article et icone-->
                                <h4>
                                    <div class="icon">
                                        <a class=""><i
                                                class="material-icons">{{ $post->icon }}</i>{!! $post->previewTitle() !!}
                                        </a>
                                    </div>
                                </h4>

                                <!-- Sous Titre de l'article -->
                                <p>{!! $post->preview() !!}</p>

                                <!-- Boutons d'actions -->
                                <div wire:click.prefetch='blockRedirection' class="align-self-end mt-auto">
                                    <div class="input-group" role="group" aria-label="Actions">
                                        @if (!$post->released && is_object($post->status))
                                            <i class="me-2 material-icons text-danger"
                                               title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                                        @endif
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
                                            <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                                            <div
                                                class="input-group-text btn-sm @if ($post->comments->count() > 0) btn-primary @else btn-secondary @endif"
                                                type="text" title="Commentaires">
                                                @if ($post->comments->count() > 0)
                                                    <span class="me-1">{{ $post->comments->count() }}</span>
                                                @endif
                                                <i class="bx bx-comment-detail"></i>
                                            </div>
                                        @endcan
                                        <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                        <button
                                            class="btn @if ($post->isFavorite()) btn-warning @else btn-secondary @endif btn-sm"
                                            title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                                            wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                            <i class="bx bx-star"></i>
                                        </button>
                                        <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                                        @can('pin')
                                            <button
                                                class="btn @if ($post->isPinned()) btn-success @else btn-secondary @endif btn-sm"
                                                title="@if ($post->isPinned()) Désépingler l'article @else épingler l'article @endif"
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
            @include('includes.pagination', ['elements' => $posts])
        </div>
    </section>
</div>
