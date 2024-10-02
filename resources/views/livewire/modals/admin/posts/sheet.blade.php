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
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">{{ $post->icon }}</span>
            <div class="ms-1 my-auto">
                <h5 class="my-auto">{{ "{$post->rubric->name} - {$post->title}" }}</h5>
            </div>
          @if (is_object($post->status))
            <span   class="ms-auto material-icons-outlined md-24"
                    title="{{ $post->status->title }}">{{ $post->status->icon }}</span>
          @endif
        </div>
    </div>
    <div class="alert alert-info mb-3">
        <p>{!! $post->content !!}</p>
    </div>
    <div class="alert alert-warning mb-0">
        <dl class="row m-0">
          @if ($post->published_at)
            <dt class="col-3 text-end ps-0 mt-3">Publié le</dt>
            <dd class="col-9 ps-0 mt-3">{{ $post->published_at->format('d/m/Y') }}</dd>
          @endif
          @if ($post->expired_at)
            <dt class="col-3 text-end ps-0">Expire le</dt>
            <dd class="col-9 ps-0">{{ $post->expired_at->format('d/m/Y') }}</dd>
          @endif
            <dt class="col-3 text-end ps-0 mt-3">Créé le</dt>
            <dd class="col-9 ps-0 mt-3">{{ "{$post->created_at->format('d/m/Y')} ({$post->author->identity})" }}</dd>
          @if ($post->corrector_id)
            <dt class="col-3 text-end ps-0">Modifié le</dt>
            <dd class="col-9 ps-0">{{ "{$post->updated_at->format('d/m/Y')} ({$post->corrector->identity})" }}</dd>
          @endif
          @if ($post->groupsWithPostRight->isNotEmpty())
            <dt class="col-12 ps-0 mt-3">Groupes ayant droits sur l'article</dt>
            <ul class="ms-2">
              @foreach ($post->groupsWithPostRight as $group)
                <li>{{ $group->name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $group->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $group->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($post->profilesWithPostRight->isNotEmpty())
            <dt class="col-12 ps-0 mt-3">Profils ayant droits sur l'article</dt>
            <ul class="ms-2">
              @foreach ($post->profilesWithPostRight as $profile)
                <li>{{ $profile->first_name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $profile->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $profile->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($post->usersWithPostRight->isNotEmpty())
            <dt class="col-12 ps-0 mt-3">Utilisateurs ayant droits sur l'article</dt>
            <ul class="ms-2">
              @foreach ($post->usersWithPostRight as $user)
                <li>{{ $user->identity }}</li>
                <dd class="col-12 px-0 mb-0">{{ $user->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 fst-italic">Ordre de priorité : {{ $user->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
@endsection
