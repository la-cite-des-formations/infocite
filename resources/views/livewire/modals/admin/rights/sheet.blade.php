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
                <span class="material-icons-outlined md-36">key</span>
            </div>
            <div class="my-auto">
                <h5 class="mb-0">{{ $right->description }}</h5>
                <div>(Par défaut : {{ $right->defaultRoles() }})</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <dt class="col-12 px-0">Description :</dt>
            <dd class="col-12 px-0">(les rôles cochés sont ceux exercés depuis le tableau de bord)</dd>
          @foreach ($roles as $role)
            <dt class="col-1 text-right px-0">
                <span class="material-icons-outlined md-18 align-text-top mr-1">
                    @if($right->exercisedFromDashboard($role->flag)) check_box @else check_box_outline_blank @endif
                </span>
            </dt>
            <dt class="col-11 px-0">{{ $role->name }} - {{ $right->{$role->title} }}</dt>
            <dd class="col-1"></dt>
            <dd class="col-11 px-0">{{ $right->{$role->description} }}</dd>
          @endforeach
        </dl>
      @if($right->groups->count())
        <dl class="row mt-2 mb-0 mx-0">
            <dt class="col-12 px-0">Groupes ayant droit :</dt>
            <ul>
              @foreach ($right->groups as $group)
                <li>{{ $group->identity().$group->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $group->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $group->pivot->priority }}</dd>
              @endforeach
            </ul>
        </dl>
      @endif
      @if($right->users->count())
        <dl class="row mt-2 mb-0 mx-0">
            <dt class="col-12 px-0">Profils et utilisateurs ayant droit :</dt>
            <ul>
              @foreach ($right->users as $user)
                <li>{{ $user->identity().$user->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $user->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $user->pivot->priority }}</dd>
              @endforeach
            </ul>
        </dl>
      @endif
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Fermer</button>
        <button wire:click="switchMode('creation')" title="{{ $addButtonTitle }}"
                type="button" class="d-flex btn btn-sm btn-success">
            <span class="material-icons">add</span>
        </button>
    </div>
@endsection
