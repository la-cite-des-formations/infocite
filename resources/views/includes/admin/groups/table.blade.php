@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-5 @cannot('deleteAny', 'App\\Group') p-2 @endcannot">
          @can('deleteAny', 'App\\Group')
            <div class="btn-group dropleft mr-1">
                <button type="button" class="d-flex btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des groupes">
                    <span class="material-icons">group</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:onchoiceSelection('group-cbx', 'all')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box</span>
                        Tous
                    </a>
                    <a href="javascript:onchoiceSelection('group-cbx', 'none')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">check_box_outline_blank</span>
                        Aucun
                    </a>
                    <a href="javascript:onchoiceSelection('group-cbx', 'reverse')" class="d-flex dropdown-item">
                        <span class="material-icons md-18 ml-0 mr-1">swap_horiz</span>
                        Inverser
                    </a>
                </div>
            </div>
          @endcan
          @cannot('deleteAny', 'App\\Group')
            <div class="btn-group mr-1">
                <span class="material-icons">group</span>
            </div>
          @endcannot
            Groupe
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">category</span>
            </div>
            Type
        </th>
        <th scope="col" class="col py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">scatter_plot</span>
            </div>
            Effectif
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\Group')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success mr-1" title="Ajouter un groupe">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', 'App\\Group')
                <button wire:click="showModal('delete', getSelectionIDs('group-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les groupes selectionnés">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($groups)
 @section('table-body')
  @foreach ($groups as $group)
   @canany(['view', 'update', 'delete'], $group)
    <tr class="row">
        <td scope="row" class="col-5">
          @can('deleteAny', 'App\\Group')
            <input type="checkbox" class="ml-0 form-check-input group-cbx" id="{{ $group->id }}">
          @endcan
            <label class="ml-4 text-primary d-flex" for="{{ $group->id }}">
                {{ $group->name }}
            </label>
        </td>
        <td scope="row" class="col-3">{{ AP::getGroupType($group->type) }}</td>
        <td scope="row" class="col">{{ $group->users->count() }}</td>
        <td class="col d-flex justify-content-end mb-auto">
          @can('view', $group)
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $group->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @canany(['update', 'handle'], $group)
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $group->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', $group)
            <a wire:click="showModal('delete', [{{ $group->id }}])"
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
