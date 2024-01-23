@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col @cannot('deleteAny', 'App\\User') p-2 @endcannot" >
          @can('deleteAny', 'App\\User')
            <div class="btn-group dropleft mr-1">
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des utilisateurs">
                    <span class="material-icons">person</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('user-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('user-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('user-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
          @endcan
          @cannot('deleteAny', 'App\\User')
            <div class="btn-group mr-1">
                <span class="material-icons">person</span>
            </div>
          @endcannot
            Utilisateur
        </th>
        <th scope="col" class="col py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">{{ $userInfo['icon'] }}</span>
            </div>
            {{ $userInfo['header'] }}
        </th>
        <th scope="col" class="col-3 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\User')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success mr-1" title="Ajouter un utilisateur">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', 'App\\User')
                <button wire:click="showModal('delete', getSelectionIDs('user-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les utilisateurs selectionnés">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($users)
 @section('table-body')
  @foreach ($users as $user)
   @canany(['view', 'update', 'delete', 'adminRights'], $user)
    <tr class="row @if($user->is_frozen) table-warning @endif">
        <td scope="row" class="col">
          @can('deleteAny', 'App\\User')
            <input type="checkbox" class="ml-0 form-check-input user-cbx" id="{{ $user->id }}">
          @endcan
            <label class="ml-4 text-primary d-flex" for="{{ $user->id }}" title="{{ $user->login }}">
                {{ $user->first_name }} {{ $user->name }}
            </label>
        </td>
        <td class="col">
            {{ $user->getInfo($userInfo) }}
        </td>
        <td class="col-3 d-flex justify-content-end mb-auto">
          @canany(['view', 'adminRights'], $user)
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $user->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @can('update', $user)
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $user->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', $user)
            <a wire:click="showModal('delete', [{{ $user->id }}])"
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
