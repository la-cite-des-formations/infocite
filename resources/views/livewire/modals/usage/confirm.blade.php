@extends('layouts.usage-modal')

  @if ($handling === 'notification')
    @section('modal-title', "Notification")
    @section('modal-body')
        <div class="alert alert-warning mb-3">
            <p>{{$message}}</p>
        </div>
    @endsection

    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    @endsection
  @else
    @section('modal-title', "Confirmation")

@section('modal-body')
    <div class="alert alert-{{ $handling === 'update' || $handling === 'create' ? 'success' : 'danger' }} mb-3">
        <p>{{$message}}</p>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-{{ $handling === 'update' || $handling === 'create' ? 'primary' : 'danger' }}" wire:click="confirm" data-bs-dismiss="modal">OK</button>
@endsection
  @endif
