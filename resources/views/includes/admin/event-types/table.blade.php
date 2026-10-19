@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-6">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des types d'événements">
                        <span class="material-icons">event</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('event-type-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('event-type-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('event-type-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
                <span class="ms-2">Nom du type d'événement</span>
            </div>
        </th>
        <th scope="col" class="col-3 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">palette</span>
                <span class="ms-1">Couleur</span>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un type d'événement">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('event-type-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les types d'événements sélectionnés">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($eventTypes)
 @section('table-body')
  @foreach ($eventTypes as $type)
    <tr class="row align-items-center">
        <td scope="row" class="col-6">
            <div class="form-check d-flex align-items-center">
                <input type="checkbox" class="form-check-input event-type-cbx me-2" id="event-type-{{ $type->id }}" value="{{ $type->id }}">
                <label class="form-check-label text-primary animate-hover" for="event-type-{{ $type->id }}">{{ $type->name }}</label>
            </div>
        </td>
        <td class="col-3">
            <div class="d-flex align-items-center">
                <span class="d-inline-block rounded-circle me-2 shadow-sm" style="width: 16px; height: 16px; background-color: {{ $type->color }}; border: 1px solid #dee2e6;"></span>
                <code>{{ $type->color }}</code>
            </div>
        </td>
        <td class="col d-flex justify-content-end align-items-center">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $type->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $type->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
            <a wire:click="showModal('delete', [{{ $type->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
