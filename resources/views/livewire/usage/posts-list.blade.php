@foreach ($posts as $i => $post)
  @can('read', $post)
   @if ($withPinning)
    <tr height="10px"></tr>
   @endif
    <tr wire:click='redirectToPost({{ $post->id }})' role="button"
        @class([
            'icon-box',
            'pinned-post' => $withPinning,
            // 'first-post' => $post->id == $posts->first()->id,
            // 'last-post' => $post->id == $posts->last()->id,
        ])>
        <td>
          @if ($withPinning)
            <span class="position-absolute start-50 bi bi-pin-angle-fill"></span>
          @endif
            <div>
                <!-- Titre de l'article et icone-->
                <h4 class="d-flex">
                    <div class="icon mb-0 me-1">
                        <i class="material-icons">{{ $post->icon }}</i>
                    </div>
                    <a>{{ $post->title }}</a>
                </h4>
                <p>{!! $post->preview() !!}</p>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                {{$post->rubric->identity()}}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center justify-content-center">
                {{$post->updated_at->format('d/m/Y')}}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="list-group list-group-horizontal" role="group" aria-label="Infos">
                  @can('viewAny', ['App\\Comment', $post->id])
                    <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                    <span @class([
                        'list-group-item',
                        'bg-primary' => $post->comments->isNotEmpty(),
                        'bg-secondary' => $post->comments->isEmpty(),
                      ]) role="label" title="{{ $post->commentsInfo() }}">
                        {{ $post->comments->count() ?: '' }}
                        <i class="bx bx-comment-detail"></i>
                    </span>
                  @endcan
                    <!-- Article deja lu ? : class success si deja lu -->
                    <span @class([
                        'list-group-item',
                        'bg-success' => $post->isRead(),
                        'bg-danger' => !$post->isRead()
                      ]) role="label" title="{{ $post->isRead() ? 'Déjà consulté' : 'Non consulté' }}">
                        <i class="bx bx-message-alt-check"></i>
                    </span>
                </div>
              @if (!$post->released && is_object($post->status))
                <i class=" material-icons text-danger m-1"
                    title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
              @endif
            </div>
        </td>
        <td>
            <div wire:click.prefetch='blockRedirection' class="d-flex align-items-center">
                <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                    <!-- Edition de l'article  -->
                  @if ($mode == 'edition')
                   @can('update', $post)
                    <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                        title="Modifier" role="button" class="btn btn-success">
                        <i class="bx bx-pencil"></i>
                    </a>
                   @endcan
                  @endif
                    <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                    <button @class([
                        "btn",
                        "btn-warning" => $post->isFavorite(),
                        "btn-secondary" => !$post->isFavorite(),
                      ]) title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                        wire:click="switchFavoritePost({{ $post->id }})" type="button">
                        <i class="bx bx-star"></i>
                    </button>
                    <!-- Epingler l'article, 4 articles épinglés à la fois maximum-->
                  @can('pin')
                    <button @class([
                        "btn",
                        "btn-success" => $post->is_pinned,
                        "btn-secondary" => !$post->is_pinned,
                      ]) title="@if ($post->is_pinned) Désépingler l'article @else épingler l'article @endif"
                        wire:click="switchPinnedPost({{ $post->id }})" type="button">
                        <i class='bx bx-pin'></i>
                    </button>
                  @endcan
                  @if ($mode == 'edition')
                   @can('delete', $post)
                    <button
                        wire:click="showModal('confirm', {handling : 'deletePostFromRubric', postId : {{ $post->id }}})"
                        type="button" class="btn btn-danger" title="Supprimer">
                        <i class="bx bx-trash"></i>
                    </button>
                   @endcan
                  @endif
                </div>
            </div>
        </td>
    </tr>
  @endcan
@endforeach
