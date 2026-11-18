@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des événements">
                        <span class="material-icons">calendar_today</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('event-cbx', 'all')" class="d-flex dropdown-item">
                                <span class="material-icons md-18 ms-0 me-1">check_box</span>
                                Tous
                            </a></li>
                        <li><a href="javascript:onchoiceSelection('event-cbx', 'none')" class="d-flex dropdown-item">
                                <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                                Aucun
                            </a></li>
                        <li><a href="javascript:onchoiceSelection('event-cbx', 'reverse')" class="d-flex dropdown-item">
                                <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                                Inverser
                            </a></li>
                    </ul>
                </div>
                <span class="ms-2">Événement</span>
            </div>
        </th>
        <th scope="col" class="col-2 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">category</span>
                <span class="ms-1">Type</span>
            </div>
        </th>
        <th scope="col" class="col-2 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">date_range</span>
                <span class="ms-1">Dates</span>
            </div>
        </th>
        <th scope="col" class="col-2 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">link</span>
                <span class="ms-1">Article lié</span>
            </div>
        </th>
        <th scope="col" class="col-2 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})" class="d-flex btn btn-sm btn-success me-1"
                    title="Ajouter un événement">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('event-cbx'))" class="d-flex btn btn-sm btn-danger"
                    title="Supprimer les événements sélectionnés">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($events)
    @section('table-body')
        @foreach ($events as $event)
            @php
                $typeColor = $event->eventType->color ?? '#3498db';
                $formattedDate = $event->start_date ? $event->start_date->format('d/m/Y') : '-';
                if ($event->end_date && $event->end_date != $event->start_date) {
                    $formattedDate .= ' au ' . $event->end_date->format('d/m/Y');
                }
            @endphp
            <tr class="row">
                <td scope="row" class="col-4">
                    <div class="form-check d-flex align-items-center">
                        <input type="checkbox" class="form-check-input event-cbx me-2" id="{{ $event->id }}"
                            value="{{ $event->id }}">
                        <label class="form-check-label text-primary text-truncate fw-semibold" for="{{ $event->id }}"
                            style="max-width: 280px;" title="{{ $event->title }}">
                            {{ $event->title }}
                        </label>
                    </div>
                </td>
                <td class="col-2">
                    <span class="badge" style="background-color: {{ $typeColor }};">
                        {{ $event->eventType->name ?? 'Général' }}
                    </span>
                </td>
                <td class="col-2 small text-secondary">
                    {{ $formattedDate }}
                    @if ($event->start_time)
                        <span class="text-muted d-block small">à {{ substr($event->start_time, 0, 5) }}</span>
                    @endif
                </td>
                <td class="col-2 small text-secondary text-truncate">
                    @if ($event->post_id && $event->post)
                        {{ $event->post->title }}
                    @else
                        <span class="text-muted fst-italic">Autonome</span>
                    @endif
                </td>
                <td class="col-2 d-flex justify-content-end align-items-center">
                    <a wire:click="showModal('edit', {mode : 'view', id : {{ $event->id }}})"
                        class="spot spot-info text-info" role="button" title="Visualiser">
                        <span class="material-icons">preview</span>
                    </a>
                    <a wire:click="showModal('edit', {mode : 'edition', id : {{ $event->id }}})"
                        class="spot spot-success text-success" role="button" title="Modifier">
                        <span class="material-icons">mode</span>
                    </a>
                    <a wire:click="showModal('delete', [{{ $event->id }}])" class="spot spot-danger text-danger"
                        role="button" title="Supprimer">
                        <span class="material-icons">delete</span>
                    </a>
                </td>
            </tr>
        @endforeach
    @endsection
@endisset
