@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4">
            <div class="btn-group dropleft mr-1">
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des noeuds graphiques">
                    <span class="material-icons">developer_board</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('chartnode-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('chartnode-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('chartnode-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
            Processus
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success mr-1" title="Ajouter un noeud graphique">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('chartnode-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les noeuds graphiques selectionnées">
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
        <td scope="row" class="col-4">
            <input type="checkbox" class="ml-0 form-check-input chartnode-cbx" id="{{ $chartnode->id }}">
            <label class="ml-4 text-primary d-flex" for="{{ $chartnode->id }}">
                {{ $chartnode->name }}
            </label>
        </td>
        <td class="col d-flex justify-content-end mb-auto">
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
