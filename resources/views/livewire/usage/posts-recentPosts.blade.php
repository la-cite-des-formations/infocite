<div class="row">
    @foreach ($recentPosts as $i => $post)
        @can('read', $post)
            <div wire:click='redirectToPost({{ $post->id }})' role="button" 
                class="recent-post col-6 mt-2 mb-3"
                @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i % 4 + 1) * 100 }}" @endif>
                
                @php
                    $bgImage = '/img/default-post-bg.png';
                    if ($post->gallery && $post->gallery->imagesCount() > 0) {
                        $bgImage = $post->gallery->sortedImages()->first()['path'];
                    }
                @endphp

                <div class="position-relative recent-post-box icon-box d-flex flex-column" 
                    style="background-image: url('{!! addslashes(asset($bgImage)) !!}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    
                    <div class="overlay"></div>
                    <div class="ribbon-new">NOUVEAU</div>

                    @if (!$post->released && is_object($post->status))
                        <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger z-index-2"
                            title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                    @endif

                    <!-- Boutons d'actions -->
                    <div wire:click.prefetch='blockRedirection' class="actions position-absolute top-0 end-0 m-2 z-index-2">
                        <div class="list-group list-group-horizontal btn-group btn-group-sm" role="group" aria-label="Actions">
                            @if ($mode == 'edition')
                                @can('update', $post)
                                    <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                        title="Modifier" role="button" class="btn btn-success small-action-btn">
                                        <i class="bx bx-pencil"></i>
                                    </a>
                                @endcan
                            @endif
                            @can('viewAny', ['App\\Models\\Comment', $post->id])
                                <button @class([
                                    'list-group-item small-action-btn',
                                    'list-group-item-primary' => $post->comments->isNotEmpty(),
                                    'list-group-item-secondary' => $post->comments->isEmpty(),
                                ]) type="text" title="{{ $post->commentsInfo() }}">
                                    {{ $post->comments->count() ? $post->comments->count() . ' ' : '' }}
                                    <i class="bx bx-comment-detail"></i>
                                </button>
                            @endcan
                            <button @class([
                                'btn small-action-btn',
                                'btn-warning' => $post->isFavorite,
                                'btn-secondary' => !$post->isFavorite,
                            ]) title="@if ($post->isFavorite) Retirer des favoris @else Ajouter aux favoris @endif"
                                wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                <i class="bx bx-star"></i>
                            </button>
                            @can('pin', $post)
                                <button @class([
                                    'btn small-action-btn',
                                    'btn-success' => $post->is_pinned,
                                    'btn-secondary' => !$post->is_pinned,
                                ]) title="@if ($post->is_pinned) Désépingler @else Épingler @endif"
                                    wire:click="switchPinnedPost({{ $post->id }})" type="button">
                                    <i class='bx bx-pin'></i>
                                </button>
                            @endcan
                            <button @class([
                                'list-group-item small-action-btn',
                                'list-group-item-success' => $post->isRead(),
                                'list-group-item-danger' => !$post->isRead(),
                            ]) type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                                <i class="bx bx-message-alt-check"></i>
                            </button>
                            @if ($mode == 'edition')
                                @can('delete', $post)
                                    <button wire:click="showModal('confirm', {handling : 'deletePostFromRubric', postId : {{ $post->id }}})"
                                        type="button" class="btn btn-danger small-action-btn" title="Supprimer">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>

                    <div class="content mt-auto z-index-2">
                        <h4 class="mb-1">
                            <i class="material-icons fs-5 align-middle me-1">{{ $post->icon }}</i>
                            <a>{{ AP::strLimiter($post->title, 50) }}</a>
                        </h4>
                        <p class="small mb-1">{!! AP::strLimiter(strip_tags($post->content), 80) !!}</p>
                    </div>
                </div>
            </div>
        @endcan
    @endforeach
</div>
