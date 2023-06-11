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
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">{{ $post->icon }}</span>
            </div>
            <div class="my-auto">
                <h5>{{ "{$post->rubric->name} - {$post->title}" }}</h5>
            </div>
            <div class="ml-auto my-auto">{{ $post->published ? 'Publié' : 'Non Publié' }}</div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <p>{!! $post->content !!}</p>
        <dl class="row mb-0 mx-0">
            <dt class="col-3 text-right pl-0">Créé le</dt>
            <dd class="col-9 pl-0">{{ "{$post->created_at->format('d/m/Y')} ({$post->author->identity()})" }}</dd>
          @if ($post->corrector_id)
            <dt class="col-3 text-right pl-0">Modifié le</dt>
            <dd class="col-9 pl-0">{{ "{$post->updated_at->format('d/m/Y')} ({$post->corrector->identity()})" }}</dd>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
