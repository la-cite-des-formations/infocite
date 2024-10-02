@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4 @cannot('deleteAny', 'App\\Rubric') p-2 @endcannot">
            <div class="d-flex align-items-center">
              @can('deleteAny', 'App\\Rubric')
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des rubriques">
                        <span class="material-icons">window</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('rubric-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('rubric-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('rubric-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
              @else
                <span class="material-icons me-1">window</span>
              @endcan
                Rubrique
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons-outlined m-0">account_tree</span>
                <div class="ms-1">Parent</div>
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">radar</span>
                <div class="ms-1">Position (ordre)</div>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @can('create', 'App\\Rubric')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter une rubrique">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', 'App\\Rubric')
                <button wire:click="showModal('delete', getSelectionIDs('rubric-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les rubriques selectionnées">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($rubrics)
 @section('table-body')
  @foreach ($rubrics as $rubric)
   @canany(['view', 'update', 'delete'], $rubric)
    <tr class="row">
        <td scope="row" class="col-4">
          @can('deleteAny', 'App\\Rubric')
            <div class="form-check">
                <input type="checkbox" class="form-check-input rubric-cbx" id="{{ $rubric->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $rubric->id }}">{{ $rubric->name }}</label>
            </div>
          @else
            <div class="text-primary">{{ $rubric->name }}</div>
          @endcan
        </td>
        <td scope="row" class="col-3">{{ $rubric->parent ? $rubric->parent->name : '-' }}</td>
        <td scope="row" class="col-3">{{ $rubric->global_position }}</td>
        <td class="col d-flex justify-content-end align-items-center">
          @can('view', $rubric)
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $rubric->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @can('update', $rubric)
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $rubric->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', $rubric)
            <a wire:click="showModal('delete', [{{ $rubric->id }}])"
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
