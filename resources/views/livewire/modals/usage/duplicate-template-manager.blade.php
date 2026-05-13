@extends('layouts.modal')

@section('modal-title', 'Dupliquer le modèle')

@section('modal-body')
    <div class="mb-3">
        <label for="new-template-name" class="form-label">Nom de la copie</label>
        <input type="text" class="form-control" id="new-template-name" wire:model.defer="newName" placeholder="Nom du nouveau modèle...">
    </div>

    @if($fromEdit)
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" wire:model.defer="saveOriginal" id="save-original">
            <label class="form-check-label" for="save-original">
                Sauvegarder les modifications sur le modèle d'origine
            </label>
        </div>
    @endif
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="duplicate(false)" data-bs-dismiss="modal">Dupliquer</button>
    <button type="button" class="btn btn-success" wire:click="duplicate(true)" data-bs-dismiss="modal">Dupliquer et éditer</button>
@endsection
