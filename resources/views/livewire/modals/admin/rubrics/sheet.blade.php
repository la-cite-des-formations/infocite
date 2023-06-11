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
                <span class="material-icons-outlined md-36">{{ $rubric->icon ?: 'window' }}</span>
            </div>
            <div class="my-auto">
                <h5>{{ $rubric->name }}</h5>
                <div class="mt-0">{{ $rubric->contains_posts ? "Avec articles ({$rubric->posts->count()})" : 'Sans articles' }}</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <dt class="col-3 text-right pl-0">Titre</dt>
            <dd class="col-9 pl-0">{{ $rubric->title }}</dd>
            <dt class="col-3 text-right pl-0">Description</dt>
            <dd class="col-9 pl-0">{{ $rubric->description }}</dd>
          @if ($rubric->parent_id)
            <dt class="col-3 text-right pl-0">Parent</dt>
            <dd class="col-9 pl-0">{{ $rubric->parent->name }}</dd>
          @endif
            <dt class="col-3 text-right pl-0">Position</dt>
            <dd class="col-9 pl-0">{{ AP::getRubricPosition($rubric->position) }}</dd>
            <dt class="col-3 text-right pl-0">Ordre</dt>
            <dd class="col-9 pl-0">{{ $rubric->rank }}</dd>
          @if ($rubric->route())
            <dt class="col-3 text-right pl-0">Route</dt>
            <dd class="col-9 pl-0">{{ $rubric->route() }}</dd>
          @endif
          @if ($rubric->childs->count())
            <dt class="col-4 text-center pl-0">Sous-rubriques</dt>
            <dd class="col-8 pl-0"></dd>
            <dt class="col-2"></dt>
            <dd class="col-10 pl-0">
                <ul>
                  @foreach ($rubric->childs as $child)
                    <li>{{ $child->name }}</li>
                  @endforeach
                </ul>
            </dd>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
