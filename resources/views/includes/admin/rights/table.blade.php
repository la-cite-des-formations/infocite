@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4 @cannot('deleteAny', 'App\\Right') p-2 @endcannot">
            <div class="d-flex align-items-center">
              @can('deleteAny', 'App\\Right')
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des droits utilisateurs">
                        <span class="material-icons">key</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('right-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('right-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('right-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
              @else
                <span class="material-icons me-1">key</span>
              @endcan
                Droits utilisateurs
            </div>
        </th>
        <th scope="col" class="col-5 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">admin_panel_settings</span>
                <div class="ms-1">Rôles exercés depuis le tableau de bord</div>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\Right')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter des droits">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', 'App\\Right')
                <button wire:click="showModal('delete', getSelectionIDs('right-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les droits selectionnés">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($rights)
 @section('table-body')
  @foreach ($rights as $right)
   @if(auth()->user()->canany(['view', 'update', 'delete'], $right) ||
        auth()->user()->can('adminRights', 'App\\User') ||
        auth()->user()->can('adminRights', ['App\\User', TRUE]))
    <tr class="row">
        <td scope="row" class="col-4">
          @can('deleteAny', 'App\\Right')
            <div class="form-check">
                <input type="checkbox" class="form-check-input right-cbx" id="{{ $right->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $right->id }}">{{ $right->description }}</label>
            </div>
          @else
            <div class="text-primary">{{ $right->description }}</div>
          @endcan
        </td>
        <td class="col-5">{{ $right->rolesFromDashboard() }}</td>
        <td class="col d-flex justify-content-end align-items-center">
          @if(auth()->user()->can('view', $right) ||
                auth()->user()->can('adminRights', 'App\\User') ||
                auth()->user()->can('adminRights', ['App\\User', TRUE]))
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $right->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endif
          @if(auth()->user()->can('update', $right) ||
                auth()->user()->can('adminRights', 'App\\User') ||
                auth()->user()->can('adminRights', ['App\\User', TRUE]))
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $right->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endif
          @can('delete', $right)
            <a wire:click="showModal('delete', [{{ $right->id }}])"
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
