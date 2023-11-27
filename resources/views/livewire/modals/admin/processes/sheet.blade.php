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
                <span class="material-icons-outlined md-36">developer_board</span>
            </div>
            <div class="my-auto">
                <h5 class="mb-0">{{ $process->name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <dt class="col-6 text-right pl-0">Groupe processus associé :</dt>
            <dd class="col-6 pl-0">{{ $process->group->name }}</dd>
          @if ($process->manager)
            <dt class="col-6 text-right pl-0">Responsable :</dt>
            <dd class="col-6 pl-0">{{ $process->manager->identity }}</dd>
          @endif
          @if ($process->actors->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Acteurs du processus</dt>
            <ul>
              @foreach ($process->actors as $actor)
                <li>{{ $actor->identity.AP::betweenBrackets($actor->pivot->function) }}</li>
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
