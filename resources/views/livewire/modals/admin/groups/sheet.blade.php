@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
  @can('update', $group)
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
  @endcan
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">groups</span>
            </div>
            <div class="my-auto">
                <h5>{{ $group->name }}</h5>
            </div>
            <div class="ml-auto my-auto">{{ AP::getGroupType($group->type) }}</div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
          @if (isset($group->code_ypareo))
            <dt class="col-3 text-right pl-0">Code YParéo</dt>
            <dd class="col-9 pl-0">{{ $group->code_ypareo }}</dd>
          @endif
            <dt class="col-12 pl-0 mt-3">Membres du groupe</dt>
            <ul>
              @if ($group->users->isNotEmpty())
               @foreach($group->users as $user)
                <li @if($user->is_frozen) class="alert-warning" @endif>
                    {{ $user->identity.$user->function($group->id, " - %%") }}
                </li>
               @endforeach
              @else
                <dl class="col-12 pl-0 font-italic">Groupe vide</dl>
              @endif
            </ul>
          @if ($group->profiles->isNotEmpty())
            <dt class="col-12 pl-0 mt-2">Profils associés</dt>
            <ul>
              @foreach ($group->profiles as $profile)
                <li>{{ $profile->first_name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($group->apps->isNotEmpty())
            <dt class="col-12 pl-0 mt-2">Applications associées</dt>
            <ul>
              @foreach ($group->apps as $app)
                <li>{{ $app->identity() }}</li>
              @endforeach
            </ul>
          @endif
          @if ($group->rights->isNotEmpty())
            <dt class="col-12 pl-0 mt-2">Droits</dt>
            <ul>
              @foreach ($group->rights->sortByDesc('pivot.priority')->sortBy('name') as $right)
                <li>{{ $right->description.$right->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $right->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $right->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
