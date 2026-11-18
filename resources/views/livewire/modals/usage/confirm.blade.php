@extends('layouts.modal')

@section('modal-title', 'Confirmation')

@section('modal-body')
    <div class="alert alert-{{ $handling === 'update' || $handling === 'create' ? 'success' : 'danger' }} mb-3">
        <p class="mb-0">{{ $message }}</p>
    </div>

    @if ($hasLinkedEvent)
        <div class="alert alert-warning d-flex align-items-center mb-0">
            <i class="bx bx-calendar-event fs-3 me-2 text-warning"></i>
            <div>
                <strong>Événement lié dans l'agenda :</strong><br>
                Cet article possède un événement dans l'agenda. Voulez-vous supprimer également l'événement lié à cet article ?
            </div>
        </div>
    @endif
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

    @if ($hasLinkedEvent)
        <button type="button" class="btn btn-warning"
            wire:click="confirm(false)" data-bs-dismiss="modal" title="Supprime l'article mais conserve l'événement de manière autonome dans l'agenda">
            Conserver l'événement seul
        </button>
        <button type="button" class="btn btn-danger"
            wire:click="confirm(true)" data-bs-dismiss="modal" title="Supprime à la fois l'article et son événement">
            Supprimer l'article et l'événement
        </button>
    @else
        <button type="button" class="btn btn-{{ $handling === 'update' || $handling === 'create' ? 'primary' : 'danger' }}"
            wire:click="confirm" data-bs-dismiss="modal">OK</button>
    @endif
@endsection
