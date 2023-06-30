@extends('layouts.usage-modal')

@section('modal-title', "Confirmation")

@section('modal-body')
@if ($handling === 'modifier')
        <div class="alert alert-success mb-3">
            <p>Êtes-vous sûr de vouloir modifier ?</p>
        </div>
@elseif($handling === 'create')
<div class="alert alert-success mb-3">
    <p>Êtes-vous sûr de vouloir créer ?</p>
</div>
@else
    <div class="alert alert-danger mb-3">
        <p>Êtes-vous sûr de vouloir supprimer ?</p>
    </div>
@endif
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="confirm">OK</button>
@endsection
