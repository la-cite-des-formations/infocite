@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">event</span>
            <div class="ms-3 flex-fill my-auto">
                <h5 class="fw-bold mb-1">{{ $eventType->name }}</h5>
                <div class="d-flex align-items-center mt-1">
                    <span class="d-inline-block rounded-circle me-2 shadow-sm" style="width: 16px; height: 16px; background-color: {{ $eventType->color }}; border: 1px solid #dee2e6;"></span>
                    <code>{{ $eventType->color }}</code>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row m-0">
            <dt class="col-12 ps-0">Événements rattachés</dt>
            <dd class="col-12 ps-0 mb-0">
                @if ($eventType->events && $eventType->events->isNotEmpty())
                    <ul class="mb-0">
                        @foreach ($eventType->events as $event)
                            <li>
                                <strong>{{ $event->post ? $event->post->title : 'Article inconnu' }}</strong>
                                <span class="text-secondary">({{ $event->start_date->format('d/m/Y') }})</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="fst-italic text-secondary">Aucun événement n'est actuellement rattaché à ce type.</span>
                @endif
            </dd>
        </dl>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-bs-dismiss="modal">Fermer</button>
    </div>
@endsection
