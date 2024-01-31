@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-5 @cannot('deleteAny', 'App\\Comment') p-2 @endcannot">
          @can('deleteAny', 'App\\Comment')
            <div class="btn-group dropleft mr-1">
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des commentaires">
                    <span class="material-icons">comment</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('comment-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('comment-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('comment-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
          @endcan
          @cannot('deleteAny', 'App\\Comment')
            <div class="btn-group mr-1">
                <span class="material-icons">comment</span>
            </div>
          @endcannot
            Commentaire
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">article</span>
            </div>
            Article
        </th>
        <th scope="col" class="col py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">face</span>
            </div>
            Auteur
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('deleteAny', 'App\\Comment')
                <button wire:click="showModal('delete', getSelectionIDs('comment-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les commentaires selectionnés">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($comments)
 @section('table-body')
  @foreach ($comments as $comment)
   @canany(['view', 'delete'], $comment)
    <tr class="row">
        <td scope="row" class="col-5">
          @can('deleteAny', 'App\\Comment')
            <input type="checkbox" class="ml-0 form-check-input comment-cbx" id="{{ $comment->id }}">
          @endcan
            <label class="ml-4 text-primary d-flex" for="{{ $comment->id }}">
                {{ $comment->content }}
            </label>
        </td>
        <td scope="row" class="col-3">{{ $comment->post->title }}</td>
        <td scope="row" class="col">{{ $comment->author->identity }}</td>
        <td class="col d-flex justify-content-end mb-auto">
          @can('view', $comment)
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $comment->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @can('delete', $comment)
            <a wire:click="showModal('delete', [{{ $comment->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
          @endcan
        </td>
    </tr>
   @endcan
  @endforeach
 @endsection
@endisset
