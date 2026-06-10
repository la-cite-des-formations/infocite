@extends('layouts.modal')

@section('modal-title', "Prévisualisation et application d'un modèle")

@section('modal-body')
    <link rel="stylesheet" href="{{ asset('css/tinymce-templates.css') }}?v={{ filemtime(public_path('css/tinymce-templates.css')) }}">
    <div class="mb-3">
        <label class="form-label fw-bold" for="template-select">Modèle</label>
        <select id="template-select" wire:model="selectedTemplateId" class="form-select border-primary border-opacity-25">
            <option value="">Choisir un modèle à utiliser...</option>
            @foreach($templates as $template)
                <option value="{{ $template->id }}">{{ $template->title }}</option>
            @endforeach
        </select>
        <div class="form-text text-primary">
            <i class="bx bx-info-circle"></i> Le contenu du modèle encadré en mode édition s'ajoutera à la fin de votre saisie actuelle.
        </div>
    </div>

    @if($selectedTemplate || $currentContent)
        <div class="mt-4">
            <h6 class="fw-bold mb-2">Prévisualisation (rendu éditeur) :</h6>
            <div class="template-preview border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                {!! $currentContent !!}
                @if($selectedTemplate)
                    {!! $selectedTemplate->content !!}
                @endif
            </div>
        </div>
    @endif
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="apply" data-bs-dismiss="modal" @if(!$selectedTemplateId) disabled @endif>Appliquer</button>
@endsection
