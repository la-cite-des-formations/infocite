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
            <div class="my-auto mr-2">
                <span class="material-icons-outlined md-36">{{ $post->icon }}</span>
            </div>
            <div class="my-auto">
                <h5>{{ "{$post->rubric->name} - {$post->title}" }}</h5>
            </div>
          @if (is_object($post->status))
            <div class="ml-auto my-auto">
                <i class="material-icons-outlined md-24"
                   title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
            </div>
          @endif
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <p>{!! $post->content !!}</p>
        <dl class="row mb-0 mx-0">
            <dt class="col-3 text-right pl-0 mt-3">Créé le</dt>
            <dd class="col-9 pl-0 mt-3">{{ "{$post->created_at->format('d/m/Y')} ({$post->author->identity})" }}</dd>
          @if ($post->corrector_id)
            <dt class="col-3 text-right pl-0">Modifié le</dt>
            <dd class="col-9 pl-0">{{ "{$post->updated_at->format('d/m/Y')} ({$post->corrector->identity})" }}</dd>
          @endif
          @if ($post->groupsWithPostRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Groupes ayant droits sur l'article</dt>
            <ul>
              @foreach ($post->groupsWithPostRight as $group)
                <li>{{ $group->name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $group->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $group->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($post->profilesWithPostRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Profils ayant droits sur l'article</dt>
            <ul>
              @foreach ($post->profilesWithPostRight as $profile)
                <li>{{ $profile->first_name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $profile->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $profile->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($post->usersWithPostRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Utilisateurs ayant droits sur l'article</dt>
            <ul>
              @foreach ($post->usersWithPostRight as $user)
                <li>{{ $user->identity }}</li>
                <dd class="col-12 px-0 mb-0">{{ $user->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $user->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
