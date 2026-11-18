@extends('layouts.modal')

@section('modal-size', 'modal-lg')

@section('modal-title')
    @if ($event)
        <span class="d-flex align-items-center flex-wrap gap-2">
            <span class="material-icons-outlined text-primary">event</span>
            <span class="fw-bold">{{ $event->title }}</span>
            @if ($event->eventType)
                <span class="badge rounded-pill"
                    style="background-color: {{ $event->eventType->color ?? '#3498db' }}; font-size: 0.75rem;">
                    {{ $event->eventType->name }}
                </span>
            @endif
        </span>
    @else
        <span>Détail de l'événement</span>
    @endif
@endsection

@section('modal-body')
    @if (!$event)
        <div class="alert alert-warning mb-0">
            Cet événement est introuvable ou a été supprimé.
        </div>
    @else
        @php
            $thumbnail = $event->getThumbnailUrl();
            $focalStyle = $event->focalPointStyle();

            // Formatage complet de la date et heure
            $formattedDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat('l j F Y');
            if ($event->start_time) {
                $formattedDate .=
                    ($event->end_date == $event->start_date && $event->end_time ? ' de ' : ' à ') .
                    substr($event->start_time, 0, 5);
            }
            if ($event->end_date) {
                $endDateFormatted = \Carbon\Carbon::parse($event->end_date)->translatedFormat('l j F Y');
                if ($event->end_date != $event->start_date) {
                    $formattedDate = 'Du ' . $formattedDate . ' au ' . $endDateFormatted;
                }
                if ($event->end_time) {
                    $formattedDate .= ' à ' . substr($event->end_time, 0, 5);
                }
            }
        @endphp

        <div class="container-fluid px-0">
            {{-- En-tête métadonnées : Date & Lieu --}}
            <div class="d-flex flex-wrap align-items-center gap-3 p-3 rounded mb-3 bg-light border">
                <div class="d-flex align-items-center text-primary">
                    <i class="bx bx-calendar fs-4 me-2"></i>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Date et heure</small>
                        <span class="fw-semibold">{{ ucfirst($formattedDate) }}</span>
                    </div>
                </div>

                @if ($event->location)
                    <div class="d-flex align-items-center text-secondary border-start ps-3">
                        <i class="bx bx-map text-danger fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Lieu</small>
                            <span class="fw-semibold">{{ $event->location }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Image d'illustration si disponible --}}
            @if ($thumbnail)
                <div class="mb-3 text-center">
                    <div class="rounded-3 shadow-sm border overflow-hidden mx-auto"
                        style="max-width: 100%; height: 260px; background: url('{{ $thumbnail }}') no-repeat center; background-size: cover; {{ $focalStyle }}">
                    </div>
                </div>
            @endif

            {{-- Description complète --}}
            <div class="mb-3">
                <h6 class="fw-bold text-dark mb-2">Description</h6>
                <div class="text-secondary p-3 bg-white border rounded">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            {{-- Lien vers l'article associé si existant --}}
            @if ($event->post_id && $event->post && $event->post->rubric)
                <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-0">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-news fs-3 me-2 text-primary"></i>
                        <div>
                            <strong>Article Info Cité associé</strong>
                            <div class="small text-muted">{{ $event->post->title }}</div>
                        </div>
                    </div>
                    <a href="{{ $event->post->route }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        Consulter l'article <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    @endif
@endsection

@section('modal-footer')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            @if ($event)
                @can('update', $event)
                    <a href="{{ route('event.edit', ['event_id' => $event->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="bx bx-pencil me-1"></i> Modifier
                    </a>
                @endcan
            @endif
        </div>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            Fermer
        </button>
    </div>
@endsection
