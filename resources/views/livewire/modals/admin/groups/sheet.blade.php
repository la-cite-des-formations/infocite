@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
  @can('update', $group)
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary me-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
  @endcan
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">groups</span>
            <h5 class="ms-1 my-auto">{{ $group->name }}</h5>
            <div class="ms-auto">{{ AP::getGroupType($group->type) }}</div>
        </div>
    </div>
    <div class="alert alert-info mb-3">
        <dl class="row m-0">
          @if (isset($group->code_ypareo))
            <dt class="col-3 text-end ps-0">Code YParéo</dt>
            <dd class="col-9 ps-0">{{ $group->code_ypareo }}</dd>
          @endif
            <dt class="col-12 ps-0">Membres du groupe</dt>
            <ul class="ms-2">
              @if ($group->users->isNotEmpty())
               @foreach($group->users as $user)
                <li @if($user->is_frozen) class="alert-warning" @endif>
                    {{ $user->identity.$user->function($group->id, " - %%") }}
                </li>
               @endforeach
              @else
                <dl class="col-12 ps-0 fst-italic">Groupe vide</dl>
              @endif
            </ul>
        </dl>
    </div>
  @if($group->profiles->isNotEmpty() || $group->apps->isNotEmpty() || $group->rights->isNotEmpty())
    <div class="alert alert-warning mb-0">
        <dl class="row m-0">
          @if ($group->profiles->isNotEmpty())
            <dt class="col-12 ps-0">Profils associés</dt>
            <ul class="ms-2">
              @foreach ($group->profiles as $profile)
                <li>{{ $profile->first_name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($group->apps->isNotEmpty())
            <dt class="col-12 ps-0">Applications associées</dt>
            <ul class="ms-2">
              @foreach ($group->apps as $app)
                <li>{{ $app->identity() }}</li>
              @endforeach
            </ul>
          @endif
          @if ($group->rights->isNotEmpty())
            <dt class="col-12 ps-0">Droits particuliers</dt>
            <ul class="ms-2">
              @foreach ($group->rights->sortByDesc('pivot.priority')->sortBy('name') as $right)
                <li>{{ $right->description.$right->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $right->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $right->pivot->priority }}</dd>
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
