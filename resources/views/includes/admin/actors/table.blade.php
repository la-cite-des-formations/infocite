@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col">
            <div class="btn-group dropleft mr-1">
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection du personnel">
                    <span class="material-icons">person</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('actor-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('actor-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('actor-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
            Acteur
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">{{ $actorInfo['icon'] }}</span>
            </div>
            {{ $actorInfo['header'] }}
        </th>
        <th scope="col" class="col py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">supervised_user_circle</span>
            </div>
            Responsable
        </th>
        <th scope="col" class="col-2 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('build', getSelectionIDs('actor-cbx'))"
                        class="d-flex btn btn-sm btn-primary mr-1" title="Générer les liens hiérarchiques">
                    <span class="material-icons-outlined">build_circle</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($actors)
 @section('table-body')
  @foreach ($actors as $actor)
    <tr class="row @if($actor->is_frozen) table-warning @endif">
        <td scope="row" class="col">
            <input type="checkbox" class="ml-0 form-check-input actor-cbx" id="{{ $actor->id }}">
            <label class="ml-4 text-primary d-flex" for="{{ $actor->id }}" title="{{ $actor->login }}">
                {{ $actor->identity() }}
            </label>
        </td>
        <td class="col-3">
            {{ $actor->getInfo($actorInfo) }}
        </td>
        <td class="col">
            {{ is_null($actor->manager) ? 'à définir' : $actor->manager->identity() }}
        </td>
        <td class="col-2 d-flex justify-content-end mb-auto">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $actor->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $actor->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
