<div>
    <section id="breadcrumbs" class="breadcrumbs">
    </section>

    <section id="post" class="services section-bg">
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
                  @can('edit', ['App\\Post', $post->rubric_id])
                    <button class="btn btn-sm btn-primary" wire:click='switchMode' type="button"
                            title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                        <span class="bx @if ($mode == 'view') bx-pencil @else bx-show @endif"></span>
                    </button>
                  @endcan
                  @if($post->rubric->name != 'Une')
                    <button class="btn @if ($isFavoriteRubric) btn-warning @else btn-secondary @endif btn-sm"
                            @if ($isFavoriteRubric) title="Retirer la rubrique {{ $post->rubric->name }} des favoris"
                            @else title="Ajouter la rubrique {{ $post->rubric->name }} aux favoris"
                            @endif
                            wire:click="switchFavoriteRubric" type="button">
                        <i class="bx bx-star"></i>
                    </button>
                  @endif
                  @if ($mode == 'edition')
                   @can('create', ['App\\Post', $post->rubric_id])
                    <a href="{{ route('post.create', ['rubric' => $post->rubric->route()]) }}" title="Commencer un nouvel article"
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
                <h2 class="title-icon"><i class="material-icons md-36 me-2">{{ $post->icon }}</i>{{ $post->title }}</h2>
                <p>@if($post->published) Publié dans @else Non publié - @endif
                    <a href="{{ route('rubric.index', ['rubric' => $post->rubric->route()]) }}">{{ $post->rubric->identity() }}</a>
                </p>
            </div>
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
                            <button wire:click="showModal('confirm', {handling : 'deletePost'})" type="button" class="btn btn-sm btn-danger" title="Supprimer">
                                <i class="bx bx-trash"></i>
                            </button>
                           @endcan
                          @endif
                           <button class="btn btn-sm @if ($isFavoritePost) btn-warning @else btn-secondary @endif"
                                    title="@if ($isFavoritePost) Retirer des favoris @else Ajouter aux favoris @endif"
                                    wire:click="switchFavoritePost" type="button">
                                <i class="bx bx-star"></i>
                            </button>
                            <div type="text" class="input-group-text btn-sm btn-primary">
                                Article vu<span class="badge bg-light text-primary mx-1">{{ $post->readers->count() }}</span>fois
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">{!! $post->content !!}</div>
              @can('viewAny', ['App\\Comment', $post->id])
                <div class="card-footer text-muted">
                    <div class="col-lg-8 why-us mt-3">
                      @if ($post->comments->count() > 1)
                        <h5>{{ $post->comments->count() }} commentaires</h5>
                      @else
                        <h5>{{ $post->comments->count() ?: 'aucun' }} commentaire</h5>
                      @endif
                      @can('create', ['App\\Comment', $post->id])
                        <div class="comment-form my-2">
                            <input wire:model='newComment' wire:keydown.enter="commentPost" type="text" placeholder="Ajouter un commentaire">
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
                                            <a data-bs-toggle="collapse" class="@if ($i) collapsed @else collapse @endif"
                                               data-bs-target="#accordion-list-{{ $i + 1 }}">
                                                {{ $comment->author->identity() }} le {{ $comment->created_at->format('d/m/Y') }}
                                                <i class="bx bx-chevron-down icon-show"></i>
                                                <i class="bx bx-chevron-up icon-close"></i>
                                            </a>
                                            <div id="accordion-list-{{ $i + 1 }}" class="collapse @if (!$i) show @endif">
                                                <p>{{ $comment->content }}</p>
                                            </div>
                                        </li>
                                      @can('delete', $comment)
                                        <button wire:click="showModal('confirm',  {handling : 'deleteComment', id : {{ $comment->id }}})" title="Supprimer ce commentaire"
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
    </section>
</div>
