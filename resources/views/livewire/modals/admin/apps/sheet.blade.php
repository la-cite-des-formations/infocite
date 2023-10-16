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
                <span class="material-icons-outlined md-36">{{ $app->icon ?: 'web' }}</span>
            </div>
            <div class="my-auto">
                <h5>{{ $app->name }}</h5>
                <div>{{ AP::getAppAuthType($app->auth_type) }}</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <!-- Propriétaire ... -->
            <dt class="col-3 text-right pl-0">Propriétaire</dt>
            <dd class="col-9 pl-0">{{ is_object($app->owner()) ? $app->owner()->identity : 'Application institutionnelle' }}</dd>
            <!-- Url https://... -->
            <dt class="col-3 text-right pl-0">Url</dt>
            <dd class="col-9 pl-0">{{ $app->url }}</dd>
            <!-- Description ... -->
            <dt class="col-3 text-right pl-0">Description</dt>
            <dd class="col-9 pl-0">{{ $app->description }}</dd>
          @if ($app->groups()->get()->count())
            {{-- liste des groupes associés --}}
            <dt class="col-12 pl-0 mt-3">Groupes associés</dt>
           @foreach ($app->groups()->get() as $group)
            <li class="col-12 mb-0">{{ $group->name }}</li>
           @endforeach
          @endif
          @if ($app->profiles->count())
            {{-- liste des profils associés --}}
            <dt class="col-12 pl-0 mt-3">Profils associés</dt>
           @foreach ($app->profiles as $profile)
            <li class="col-12 mb-0">{{ $profile->first_name }}</li>
           @endforeach
          @endif
          @if ($app->realUsers->count())
            {{-- liste des profils associés --}}
            <dt class="col-12 pl-0 mt-3">Utilisateurs associés</dt>
           @foreach ($app->realUsers as $user)
            <li class="col-12 mb-0">{{ $user->identity }}</li>
           @endforeach
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
