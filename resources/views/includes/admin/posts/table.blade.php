@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des articles">
                        <span class="material-icons">article</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('post-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('post-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('post-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
                Article
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">history_edu</span>
                <div class="ms-1">Rédacteur</div>
            </div>
        </th>
        <th scope="col" class="col-2 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">fact_check</span>
                <div class="ms-1">État</div>
            </div>
        </th>
        <th scope="col" class="col-3 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un article">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('post-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les articles selectionnés">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($posts)
 @section('table-body')
  @foreach ($posts as $post)
    <tr class="row">
        <td scope="row" class="col">
            <div class="form-check">
                <input type="checkbox" class="form-check-input post-cbx" id="{{ $post->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $post->id }}">{{ $post->title }}</label>
            </div>
        </td>
        <td class="col-3">
            {{ $post->author->identity }}
        </td>
        <td class="col-2">
            {{ is_object($post->status) ? $post->status->title : '-' }}
        </td>
        <td class="col-3 d-flex justify-content-end align-items-center">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $post->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $post->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
            <a wire:click="showModal('delete', [{{ $post->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
