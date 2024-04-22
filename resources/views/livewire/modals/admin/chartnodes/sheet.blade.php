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
                <span class="material-icons-outlined md-36">crop_din</span>
            </div>
            <div class="my-auto">
                <h5 class="mb-0">{{ $chartnode->name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <dt class="col-6 text-right pl-0">Fonction / Processus :</dt>
            <dd class="col-6 pl-0">{{ is_object($chartnode->group) ? $chartnode->group->name : 'aucun' }}</dd>
          @if ($chartnode->actors->isNotEmpty())
            <dt class="col-12 pl-0">Acteurs</dt>
            <ul class="col-12">
              @foreach ($chartnode->actors as $actor)
                <li>{{ $actor->identity }}</li>
              @endforeach
            </ul>
          @endif
            <dt class="col-6 text-right pl-0 mt-3">Noeud parent : </dt>
            <dd class="col-6 pl-0 mt-3">{{ is_object($chartnode->parent) ? $chartnode->parent->name : 'aucun' }}</dd>
          @if ($chartnode->childs->isNotEmpty())
            <dt class="col-12 pl-0">Noeuds enfants</dt>
            <ul class="col-12">
              @foreach ($chartnode->childs as $childNode)
                <li>{{ $childNode->name }}</li>
              @endforeach
            </ul>
          @endif
      </dl>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Fermer</button>
    </div>
@endsection
