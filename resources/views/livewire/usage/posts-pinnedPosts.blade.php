<div class="row">
  @foreach ($pinnedPost as $i => $post)
   @can('read', $post)
    <div wire:click='redirectToPost({{ $post->id }})' role="button"
        class="pinned-post col-sm-12 col-md-6 col-lg-3 d-flex align-items-stretch mt-2 mb-3"
        @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 4 + 1) * 100 }}" @endif>
        <div class="position-relative icon-box flex-fill d-flex flex-column">
            <span class="position-absolute start-50 bi bi-pin-angle-fill"></span>
          @if (!$post->released && is_object($post->status))
            <i class="position-absolute top-0 end-0 mt-3 me-3 material-icons text-danger"
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
            <div wire:click.prefetch='blockRedirection' class="align-self-end mt-auto">
                <div class="list-group list-group-horizontal btn-group btn-group-sm" role="group" aria-label="Actions">
                  @if ($mode == 'edition')
                   @can('update', $post)
                    <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                        title="Modifier" role="button" class="btn btn-success d-flex">
                        <i class="bx bx-pencil my-auto"></i>
                    </a>
                   @endcan
                  @endif
                  @can('viewAny', ['App\\Models\\Comment', $post->id])
                    <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                    <button @class([
                                "list-group-item py-1 px-2",
                                "list-group-item-primary" => $post->comments->isNotEmpty(),
                                "list-group-item-secondary" => $post->comments->isEmpty(),
                            ]) title="{{ $post->commentsInfo() }}" type="text">
                        {{ $post->comments->count() ?: '' }}
                        <i @class(["bx bx-comment-detail", "ms-1" => $post->comments->isNotEmpty()])></i>
                    </button>
                  @endcan
                    <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                    <button @class([
                                "btn",
                                "btn-warning" => $post->isFavorite(),
                                "btn-secondary" => !$post->isFavorite(),
                            ]) title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                            wire:click="switchFavoritePost({{ $post->id }})" type="button">
                        <i class="bx bx-star"></i>
                    </button>
                  @can('pin')
                    <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                    <button @class([
                                "btn",
                                "btn-success" => $post->is_pinned,
                                "btn-secondary" => !$post->is_pinned,
                            ]) title="@if ($post->is_pinned) Désépingler l'article @else épingler l'article @endif"
                            wire:click="switchPinnedPost({{ $post->id }})" type="button">
                        <i class='bx bx-pin'></i>
                    </button>
                  @endcan
                    <!-- Article deja lu ? : class success si deja lu -->
                    <button @class([
                                "list-group-item py-1 px-2",
                                "list-group-item-success" => $post->isRead(),
                                "list-group-item-danger" => !$post->isRead(),
                            ])  type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                        <i class="bx bx-message-alt-check"></i>
                    </button>
                  @if ($mode == 'edition')
                   @can('delete', $post)
                    <button wire:click="showModal('confirm', {handling : 'deletePostFromRubric', postId : {{ $post->id }}})"
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
