@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
  @can('update', $right)
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary me-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
  @endcan
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">key</span>
            <div class="ms-1 my-auto">
                <h5 class="my-auto">{{ $right->description }}</h5>
                <div>(par défaut : {{ $right->defaultRoles() }})</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-3">
        <dl class="row m-0">
            <dt class="col-12 px-0">Description :</dt>
            <dd class="col-12 px-0">(rôles cochés exercés exclusivement depuis le tableau de bord)</dd>
          @foreach ($roles as $role)
            <dt class="col-1 text-end px-0">
                <span class="material-icons-outlined md-18 align-text-top me-1">
                    @if($right->exercisedFromDashboard($role->flag)) check_box @else check_box_outline_blank @endif
                </span>
            </dt>
            <dt class="col-11 px-0">{{ $role->name }} - {{ $right->{$role->title} }}</dt>
            <dd class="col-1"></dd>
            <dd class="col-11 px-0">{{ $right->{$role->description} }}</dd>
          @endforeach
        </dl>
    </div>
    <div class="alert alert-warning mb-0">
        <dl class="row m-0">
          @if($right->groups->isNotEmpty())
            <dt class="col-12 px-0">Groupes ayant droit :</dt>
            <ul class="ms-2">
              @foreach ($right->groups as $group)
                <li>{{ $group->identity().$group->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $group->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $group->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if($right->profiles->isNotEmpty())
            <dt class="col-12 px-0">Profils ayant droit :</dt>
            <ul class="ms-2">
              @foreach ($right->profiles as $profile)
                <li>{{ $profile->first_name.$profile->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $profile->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $profile->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if($right->realUsers->isNotEmpty())
            <dt class="col-12 px-0">Utilisateurs ayant droit :</dt>
            <ul class="ms-2">
              @foreach ($right->realUsers as $user)
                <li>{{ $user->identity.$user->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $user->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $user->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
    </div>
@endsection
