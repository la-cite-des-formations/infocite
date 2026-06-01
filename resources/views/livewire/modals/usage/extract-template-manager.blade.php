@extends('layouts.modal')

@section('modal-title', "Extraction d'un modèle")

@section('modal-body')
    <link rel="stylesheet" href="{{ asset('css/tinymce-templates.css') }}">
    <div class="mb-3">
        <p>
            Vous êtes sur le point de créer un nouveau modèle d'article à partir de ce contenu.
            <br>La pré-configuration (titre, icône, cadres) sera conservée, mais les options spécifiques (dates, accusé de lecture...) seront réinitialisées.
        </p>
    </div>

    @if($isDirty)
    <div class="alert alert-warning py-2 mb-3">
        <div class="d-flex align-items-center">
            <i class="bx bx-error-circle fs-4 me-2"></i>
            <div>
                <strong>L'article actuel a été modifié.</strong>
            </div>
        </div>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" wire:model="saveArticleBeforeRedirect" id="saveArticleCheck">
            <label class="form-check-label" for="saveArticleCheck">
                Enregistrer d'abord ces modifications dans l'article actuel en cas de redirection vers le modèle.
            </label>
        </div>
    </div>
    @endif

    <div class="mt-4">
        <h6 class="fw-bold mb-2">Aperçu du futur modèle :</h6>
        <div class="template-preview border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
            {!! $currentContent !!}
        </div>
    </div>

@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="extractAndStay" data-bs-dismiss="modal">Extraire et Rester</button>
    <button type="button" class="btn btn-success" wire:click="extractAndRedirect" data-bs-dismiss="modal">Extraire et Éditer</button>
@endsection
