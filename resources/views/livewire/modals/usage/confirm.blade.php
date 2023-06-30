@extends('layouts.usage-modal')

@section('modal-title', "Confirmation")

@section('modal-body')
    <div class="alert alert-success mb-3">
        <p>{{$message}}</p>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="confirm">OK</button>
@endsection
