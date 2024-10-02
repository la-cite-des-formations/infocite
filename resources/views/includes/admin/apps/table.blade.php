@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="@if ($filter['type'] != 'I') col-3 @else col-4 @endif">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des applications">
                        <span class="material-icons">web</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('app-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('app-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('app-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
                {{ $filter['type'] == 'I' ? 'Application institutionnelle' : 'Application' }}
            </div>
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">link</span>
                <div class="ms-1">Url</div>
            </div>
        </th>
      @if ($filter['type'] != 'I')
        <th scope="col" class="col-2 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">co_present</span>
                <div class="ms-1">Propriétaire</div>
            </div>
        </th>
      @endif
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
              @canany(['create', 'createFor'], 'App\\App')
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter une application">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', 'App\\App')
                <button wire:click="showModal('delete', getSelectionIDs('app-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les applications selectionnées">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($apps)
 @section('table-body')
  @foreach ($apps as $app)
    <tr class="row">
        <td scope="row" class="col-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input app-cbx" id="{{ $app->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $app->id }}">{{ $app->name }}</label>
            </div>
        </td>
        <td class="col-5">{{ $app->url }}</td>
      @if ($filter['type'] != 'I')
        <td class="col-2">{{ is_object($app->owner()) ? $app->owner()->identity : 'Appli institutionnelle' }}</td>
      @endif
        <td class="col d-flex justify-content-end align-items-center">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $app->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @canany(['update', 'updateFor'], $app)
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $app->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', $app)
            <a wire:click="showModal('delete', [{{ $app->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
          @endcan
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
