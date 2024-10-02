@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-5 @cannot('deleteAny', 'App\\Comment') p-2 @endcannot">
            <div class="d-flex align-items-center">
              @can('deleteAny', 'App\\Comment')
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des commentaires">
                        <span class="material-icons">comment</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('comment-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('comment-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('comment-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </diulv>
                </div>
              @else
                <span class="material-icons me-1">comment</span>
              @endcan
                Commentaire
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">article</span>
                <div class="ms-1">Article</div>
            </div>
        </th>
        <th scope="col" class="col py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">face</span>
                <div class="ms-1">Auteur</div>
            </div>
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
            <div class="form-check">
                <input type="checkbox" class="form-check-input comment-cbx" id="{{ $comment->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $comment->id }}">{{ $comment->content }}</label>
            </div>
          @else
            <div class="text-primary">{{ $comment->content }}</div>
          @endcan
        </td>
        <td scope="row" class="col-3">{{ $comment->post->title }}</td>
        <td scope="row" class="col">{{ $comment->author->identity }}</td>
        <td class="col d-flex justify-content-end align-items-center">
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
