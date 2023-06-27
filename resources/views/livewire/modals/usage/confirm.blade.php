@extends('layouts.modal')

@section('modal-title', "Confirmation")

@section('modal-body')
    <div class="alert alert-success mb-3">
        <p>Confirm confirm</p>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-secondary" wire:click='confirm'>OK</button>
@endsection
