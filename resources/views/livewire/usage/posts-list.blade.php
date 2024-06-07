@foreach ($posts as $i => $post)
    @can('read', $post)
        <tr wire:click='redirectToPost({{ $post->id }})' role="button"
            class="@if($post->is_pinned) pinnedPost @endif icon-box"
            @class(['pinnedPost'=>$post->is_pinned])>
            <td class="d-flex flex-row">
                <!-- Titre de l'article et icone-->

                <div class="icon mt-2">
                    @if($post->is_pinned)
                        <i class="material-icons me-2 ms-1 text-danger bx bxs-pin bx-rotate-90" title="pinnedIcon"></i>
                    @else
                        <i class="material-icons me-2 ms-1">{{ $post->icon }}</i>
                    @endif
                </div>
                <div class="d-flex flex-column">
                    <h5 class="align-items-center mt-2 mb-0">
                        {{ $post->previewTitle() }}
                    </h5>
                    <span class="title-description">{!! $post->preview() !!}</span>
                </div>
            </td>
            <td>
                {{$post->rubric->name}}
            </td>
            <td class="text-center">
                <span>{{$post->updated_at->format('d/m/Y')}}</span>
            </td>
            <td class="position-relative">
                <div wire:click.prefetch='blockRedirection' class=" align-self-end mt-auto">

                    <div class="input-group " role="group" aria-label="Actions">
                        <!-- Article publié ou non (pas un bouton d'action) -->
                        @if ($mode == 'edition')
                            @can('update', $post)
                                <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                                   title="Modifier" role="button"
                                   class="btn btn-sm btn-success">
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
                                    <span
                                        class="me-1">{{ $post->comments->count() }}</span>
                                @endif
                                <i class="bx bx-comment-detail"></i>
                            </div>
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
                                    type="button" class="btn btn-sm btn-danger"
                                    title="Supprimer">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endcan
                        @endif
                        @if (!$post->released && is_object($post->status))
                            <i class=" material-icons text-danger m-1"
                               title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                        @endif
                    </div>
                </div>
            </td>

            <td>
                <div wire:click.prefetch='blockRedirection'
                     class="position-relative align-self-end mt-auto">
                    <div class="input-group " role="group" aria-label="Actions">
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
                                wire:click="switchPinnedPost({{ $post->id }})"
                                type="button">
                                <i class='bx bx-pin'></i>
                            </button>
                        @endcan
                    </div>
                </div>
            </td>
        </tr>
    @endcan
@endforeach
