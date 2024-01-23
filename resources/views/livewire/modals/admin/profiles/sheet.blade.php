@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
  @can('update', [$profile, TRUE])
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
                <span class="material-icons md-36">portrait</span>
            </div>
            <div class="my-auto">
                <h5>{{ $profile->first_name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
          @foreach (AP::getGroupTypes() as $typeKey => $typeName)
           @if ($profile->groups([$typeKey])->get()->isNotEmpty())
            <dt class="col-12 mt-2 pl-0">{{ AP::getGroupFilter($typeKey)['dtLabel'] }}</dt>
            <ul>
              @foreach($profile->groups([$typeKey])->get() as $group)
                <li>{{ $group->name . ($group->pivot->function ? " ({$group->pivot->function})" : '') }}</li>
              @endforeach
            </ul>
           @endif
          @endforeach
          @if ($profile->apps->isNotEmpty())
            <dt class="col-12 mt-2 pl-0">Applications associées</dt>
            <ul>
              @foreach($profile->apps as $app)
                <li>{{ $app->name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($profile->users->isNotEmpty())
            <dt class="col-12 mt-2 pl-0">Utilisateurs associés</dt>
            <ul>
              @foreach($profile->users as $user)
                <li>{{ $user->identity }}</li>
              @endforeach
            </ul>
          @endif
          @if ($profile->allRights()->isNotEmpty())
            <dt class="col-12 pl-0 mt-2">Droits appliqués</dt>
            <ul>
              @foreach ($profile->allRights()->sortByDesc('pivot.priority')->groupBy('name')->sortKeys() as $rights)
                <li>{{ $rights->first()->description.$rights->first()->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $rights->first()->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $rights->first()->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
