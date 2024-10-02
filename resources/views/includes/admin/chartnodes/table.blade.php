@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-3">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des noeuds graphiques">
                        <span class="material-icons">pages</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('chartnode-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('chartnode-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('chartnode-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
                Noeud
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">pages</span>
                <div class="ms-1">Noeud parent</div>
            </div>
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">build</span>
                <div class="ms-1">Fonction associée</div>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un noeud graphique">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('chartnode-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les noeuds graphiques selectionnés">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($chartnodes)
 @section('table-body')
  @foreach ($chartnodes as $chartnode)
    <tr class="row">
        <td scope="row" class="col-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input chartnode-cbx" id="{{ $chartnode->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $chartnode->id }}">{{ $chartnode->name }}</label>
            </div>
        </td>
        <td class="col-3">{{ is_object($chartnode->parent) ? $chartnode->parent->name : '-' }}</td>
        <td class="col-4">{{ is_object($chartnode->group) ? $chartnode->group->name : '-' }}</td>
        <td class="col d-flex justify-content-end align-items-center">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $chartnode->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $chartnode->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
            <a wire:click="showModal('delete', [{{ $chartnode->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
