@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
  @canany(['update', 'updateFor'], $app)
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary me-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
  @endcan
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">{{ $app->icon ?: 'web' }}</span>
            <div class="ms-1 my-auto">
                <h5 class="my-auto">{{ $app->name }}</h5>
                <div>{{ AP::getAppAuthType($app->auth_type) }}</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-3">
        <dl class="row m-0">
            <!-- Propriétaire ... -->
            <dt class="col-3 text-end ps-0">Propriétaire</dt>
            <dd class="col-9 ps-0">{{ is_object($app->owner()) ? $app->owner()->identity : 'Application institutionnelle' }}</dd>
            <!-- Url https://... -->
            <dt class="col-3 text-end ps-0">Url</dt>
            <dd class="col-9 ps-0 text-truncate">{{ $app->url }}</dd>
            <!-- Description ... -->
            <dt class="col-3 text-end ps-0">Description</dt>
            <dd class="col-9 ps-0">{{ $app->description }}</dd>
        </dl>
    </div>
  @if ($app->groups()->get()->isNotEmpty() || $app->profiles->isNotEmpty() || $app->realUsers->isNotEmpty())
    <div class="alert alert-warning mb-0">
        <dl class="row m-0">
          @if ($app->groups()->get()->isNotEmpty())
            {{-- liste des groupes associés --}}
            <dt class="col-12 ps-0">Groupes associés</dt>
            <ul class="ms-2">
              @foreach ($app->groups()->get() as $group)
                <li>{{ $group->name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($app->profiles->isNotEmpty())
            {{-- liste des profils associés --}}
            <dt class="col-12 ps-0">Profils associés</dt>
            <ul class="ms-2">
              @foreach ($app->profiles as $profile)
                <li>{{ $profile->first_name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($app->realUsers->isNotEmpty())
            {{-- liste des profils associés --}}
            <dt class="col-12 ps-0">Utilisateurs associés</dt>
            <ul class="ms-2">
              @foreach ($app->realUsers as $user)
                <li>{{ $user->identity }}</li>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
  @endif
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
@endsection
