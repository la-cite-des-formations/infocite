@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary me-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    @php
        $typeColor = $event->eventType->color ?? '#3498db';
        $formattedDate = $event->start_date ? $event->start_date->format('d/m/Y') : '-';
        if ($event->end_date && $event->end_date != $event->start_date) {
            $formattedDate .= ' au ' . $event->end_date->format('d/m/Y');
        }
    @endphp

    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">event</span>
            <div class="ms-2 flex-fill my-auto">
                <h5 class="fw-bold mb-1">{{ $event->title }}</h5>
                <span class="badge" style="background-color: {{ $typeColor }};">
                    {{ $event->eventType->name ?? 'Général' }}
                </span>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-3">
        <dl class="row m-0">
            <dt class="col-3 text-end ps-0">Dates</dt>
            <dd class="col-9 ps-0">
                {{ $formattedDate }}
                @if ($event->start_time)
                    <span class="text-muted">de {{ substr($event->start_time, 0, 5) }}</span>
                    @if ($event->end_time)
                        <span class="text-muted">à {{ substr($event->end_time, 0, 5) }}</span>
                    @endif
                @endif
            </dd>

            @if ($event->location)
                <dt class="col-3 text-end ps-0">Lieu</dt>
                <dd class="col-9 ps-0">{{ $event->location }}</dd>
            @endif

            <dt class="col-3 text-end ps-0">Article lié</dt>
            <dd class="col-9 ps-0">
                @if ($event->post_id && $event->post)
                    <a href="{{ $event->post->route }}" class="text-decoration-none fw-semibold">
                        <i class="bx bx-file align-middle text-primary me-1"></i>{{ $event->post->title }}
                    </a>
                @else
                    <span class="text-muted fst-italic">Événement autonome (aucun article lié)</span>
                @endif
            </dd>

            <dt class="col-3 text-end ps-0">Description</dt>
            <dd class="col-9 ps-0" style="white-space: pre-line;">{{ $event->description }}</dd>

            @if ($event->image)
                <dt class="col-3 text-end ps-0">Illustration</dt>
                <dd class="col-9 ps-0">
                    <img src="{{ $event->getThumbnailUrl() }}" alt="{{ $event->title }}"
                        class="rounded border shadow-sm" style="max-height: 120px; max-width: 200px; object-fit: cover;">
                </dd>
            @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Fermer</button>
    </div>
@endsection
