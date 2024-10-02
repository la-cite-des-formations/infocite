@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col @cannot('deleteAny', 'App\\User') p-2 @endcannot" >
            <div class="d-flex align-items-center">
              @can('deleteAny', 'App\\User')
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des utilisateurs">
                        <span class="material-icons">person</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('user-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('user-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('user-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
              @else
                <span class="material-icons me-1">person</span>
              @endcan
                Utilisateur
            </div>
        </th>
        <th scope="col" class="col py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">{{ $userInfo['icon'] }}</span>
                <div class="ms-1">{{ $userInfo['header'] }}</div>
            </div>
        </th>
        <th scope="col" class="col-3 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\User')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un utilisateur">
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
   @if(auth()->user()->canany(['view', 'update', 'delete'], $user) || auth()->user()->can('adminRights', 'App\\User'))
    <tr class="row @if($user->is_frozen) table-warning @endif">
        <td scope="row" class="col">
          @can('deleteAny', 'App\\User')
            <div class="form-check">
                <input type="checkbox" class="form-check-input user-cbx" id="{{ $user->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $user->id }}" title="{{ $user->login }}">{{ $user->identity }}</label>
            </div>
          @else
            <div class="text-primary">{{ $user->identity }}</div>
          @endcan
        </td>
        <td class="col">{{ $user->getInfo($userInfo) }}</td>
        <td class="col-3 d-flex justify-content-end align-items-center">
          @if(auth()->user()->can('view', $user) || auth()->user()->can('adminRights', 'App\\User'))
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $user->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endif
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
   @endif
  @endforeach
 @endsection
@endisset
