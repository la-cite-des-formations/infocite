@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4 @cannot('deleteAny', 'App\\Right') p-2 @endcannot">
            <div class="btn-group dropleft mr-1">
              @can('deleteAny', 'App\\Right')
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des droits utilisateurs">
                    <span class="material-icons">key</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('right-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('right-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('right-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
          @endcan
          @cannot('deleteAny', 'App\\Right')
            <div class="btn-group mr-1">
                <span class="material-icons">key</span>
            </div>
          @endcannot
            Droits utilisateurs
        </th>
        <th scope="col" class="col-5 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">admin_panel_settings</span>
            </div>
            Rôles exercés depuis le tableau de bord
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\Right')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success mr-1" title="Ajouter des droits">
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
   @canany(['view', 'update', 'delete'], $right)
    <tr class="row">
        <td scope="row" class="col-4">
          @can('deleteAny', 'App\\Right')
            <input type="checkbox" class="ml-0 form-check-input right-cbx" id="{{ $right->id }}">
          @endcan
            <label class="ml-4 text-primary d-flex" for="{{ $right->id }}">
                {{ $right->description }}
            </label>
        </td>
        <td class="col-5">{{ $right->rolesFromDashboard() }}</td>
        <td class="col d-flex justify-content-end mb-auto">
          @can('view', $right)
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $right->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @can('update', $right)
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $right->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', $right)
            <a wire:click="showModal('delete', [{{ $right->id }}])"
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
