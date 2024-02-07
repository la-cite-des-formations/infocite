<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

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
                    <a href="{{ route('post.create', ['rubric' => $rubric->route()]) }}" title="Commencer un nouvel article"
                       type="button" class="d-flex input-group-text btn btn-sm btn-success">
                        <span class="material-icons">add</span>
                    </a>
                   @endcan
                  @endif
                </div>
            </div>
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
                              @if ($mode == 'edition')
                               @can('update', $post)
                                <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                    title="Modifier" role="button" class="btn btn-sm btn-success">
                                    <i class="bx bx-pencil"></i>
                                </a>
                               @endcan
                              @endif
                              @if($post->isCommentable())
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
                              @if ($mode == 'edition')
                               @can('delete', $post)
                                <button wire:click="showModal('confirm', {handling : 'deletePostFromRubric', postId : {{ $post->id }}})" type="button" class="btn btn-sm btn-danger" title="Supprimer">
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
