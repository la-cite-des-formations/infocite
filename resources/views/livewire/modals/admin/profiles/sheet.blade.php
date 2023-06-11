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
                <span class="material-icons md-36">portrait</span>
            </div>
            <div class="my-auto">
                <h5>{{ $profile->first_name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
          @if ($profile->groups(['C'])->count())
            <dt class="col-3 text-right pl-0">Classe</dt>
           @foreach($profile->groups(['C'])->get() as $class)
            <dd class="col-9 pl-0">{{ $class->name . ($class->pivot->function ? " ({$class->pivot->function})" : '') }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
          @if ($profile->groups(['E'])->count())
            <dt class="col-3 text-right pl-0">EP</dt>
           @foreach($profile->groups(['E'])->get() as $class)
            <dd class="col-9 pl-0">{{ $class->name . ($class->pivot->function ? " ({$class->pivot->function})" : '') }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
          @if ($profile->groups(['P'])->count())
            <dt class="col-3 text-right pl-0">Processus</dt>
           @foreach($profile->groups(['P'])->get() as $service)
            <dd class="col-9 pl-0">{{ $service->name . ($service->pivot->function ? " ({$service->pivot->function})" : '') }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
          @if ($profile->groups(['A'])->count())
            <dt class="col-3 text-right pl-0">Autres</dt>
           @foreach($profile->groups(['A'])->get() as $group)
            <dd class="col-9 pl-0">{{ $group->name . ($group->pivot->function ? " ({$group->pivot->function})" : '') }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
          @if ($profile->groups(['S'])->count())
            <dt class="col-3 text-right pl-0">Système</dt>
           @foreach($profile->groups(['S'])->get() as $group)
            <dd class="col-9 pl-0">{{ $group->name . ($group->pivot->function ? " ({$group->pivot->function})" : '') }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
          @if ($profile->apps->count())
            <dt class="col-3 text-right pl-0">Applications</dt>
           @foreach($profile->apps as $app)
            <dd class="col-9 pl-0">{{ $app->name }}</dd>
            <dd class="col-3 pl-0"></dd>
           @endforeach
            <dd class="col-9 pl-0"></dd>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
