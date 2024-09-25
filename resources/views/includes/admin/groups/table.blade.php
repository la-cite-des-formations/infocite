@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-5 @cannot('deleteAny', 'App\\Group') p-2 @endcannot">
            <div class="d-flex align-items-center">
              @can('deleteAny', 'App\\Group')
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des groupes">
                        <span class="material-icons">group</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('group-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('group-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('group-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
              @else
                <span class="material-icons me-1">group</span>
              @endcan
                Groupe
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">category</span>
                <div class="ms-1">Type</div>
            </div>
        </th>
        <th scope="col" class="col py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">scatter_plot</span>
                <div class="ms-1">Effectif</div>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\Group')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un groupe">
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
   @canany(['view', 'update', 'handle', 'delete'], $group)
    <tr class="row">
        <td scope="row" class="col-5">
          @can('deleteAny', 'App\\Group')
            <div class="form-check">
                <input type="checkbox" class="form-check-input group-cbx" id="{{ $group->id }}">
                <label class="form-check-label text-primary" for="{{ $group->id }}">{{ $group->name }}</label>
            </div>
          @else
            <div class="text-primary">{{ $group->name }}</div>
          @endcan
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
