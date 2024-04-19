<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>
{{--Liste des rubrics en favori--}}
    <section id="myFavoritesRubrics" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="bx bx-star me-1"></i>Mes favoris</h2>
            <h4>Mes rubrics favorites</h4>
        </div>
        <div class="container-fluid d-flex flex-row flex-wrap w-75 m-auto">
            @if($rubrics->count() > 0)
                @foreach($rubrics as $rubric)
                    @can('access', $rubric)

                        <a href="{{$rubric->route()}}" class="position-relative d-block btn btn-dark m-2 p-3 col-2 text-center align-items-center d-flex justify-content-center">{{$rubric->name}}
                        <button class="position-absolute top-0 end-0 btn btn-outline border-0 p-0"
                                title="Retirer des favoris"
                                wire:click.prevent="removeFavoriteRubric({{$rubric->id}})" type="button">
                            <i class='bx bxs-message-square-x bx-rotate-90' style='color:rgba(255,1,51,0.99)'  ></i>
                        </button>
                    </a>
                    @endcan
                @endforeach
            @else
                <p class="fst-italic m-auto">Aucune rubics en favoris</p>
            @endif
        </div>
    </section>
{{--Liste des articles en favoris--}}
    <section id="posts" class="services section-bg">
        <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="section-title">
                <div class="section-title">
                    <h2 class="title-icon"></h2>
                    <h4>Mes articles favoris</h4>
                </div>
            </div>
            <div class="row">
                @if($posts->count() > 0)
                @foreach ($posts as $i => $post)
                    @can('read', $post)
                        <div wire:click='redirectToPost({{ $post->id }})' role="button"
                             class="col-sm-12 col-md-6 col-lg-3 d-flex align-items-stretch mt-2 mb-3"
                             @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 4 + 1) * 100 }}" @endif>
                            <div class="position-relative icon-box d-flex flex-column">
                                @if (!$post->released && is_object($post->status))
                                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger"
                                       title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                                @endif
                                <!-- Titre de l'article -->
                                <h4>
                                    <!-- Icone -->
                                    <div class="icon"><i class="material-icons">{{ $post->icon }}</i></div>
                                    <a>{{ $post->title }}</a>
                                </h4>

                                <!-- Sous Titre de l'article -->
                                <p>{!! $post->preview() !!}</p>

                                <!-- Boutons d'actions -->
                                <div wire:click.prefetch='blockRedirection' class="align-self-end mt-auto">
                                    <div class="input-group" role="group" aria-label="Actions">
                                        @can('viewAny', ['App\\Comment', $post->id])
                                            <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                                            <div class="input-group-text btn-sm @if ($post->comments->count() > 0) btn-primary @else btn-secondary @endif"
                                                 type="text" title="Commentaires">
                                                @if ($post->comments->count() > 0)
                                                    <span class="me-1">{{ $post->comments->count() }}</span>
                                                @endif
                                                <i class="bx bx-comment-detail"></i>
                                            </div>
                                        @endif
                                        <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                        <button class="btn @if ($post->isFavorite()) btn-warning @else btn-secondary @endif btn-sm"
                                                title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                                                wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                            <i class="bx bx-star"></i>
                                        </button>
                                        <!-- Article deja lu ? : class success si deja lu -->
                                        <div class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                                             type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                                            <i class="bx bx-message-alt-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endforeach
            </div>
            @include('includes.pagination', ['elements' => $posts])
            @else
                <p class="fst-italic text-center">Aucun article en favoris</p>
            @endif
        </div>
    </section>
</div>
