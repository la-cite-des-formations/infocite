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
                <span class="material-icons-outlined md-36">{{ $rubric->icon ?: 'window' }}</span>
            </div>
            <div class="my-auto">
                <h5>{{ $rubric->identity() }}</h5>
              @if ($rubric->route())
                <div class="mt-0">Route : {{ $rubric->route() }}</div>
              @endif
                <div class="mt-0">
                  @switch(TRUE)
                   @case($rubric->is_parent)
                    Rubrique parent
                   @break

                   @case(!$rubric->is_parent && $rubric->contains_posts)
                    Rubrique avec articles ({{$rubric->posts->count()}} actuellement)
                   @break

                   @case(!$rubric->is_parent && !$rubric->contains_posts)
                    Rubrique particulière
                   @break
                  @endswitch
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <dt class="col-3 text-right pl-0">Titre</dt>
            <dd class="col-9 pl-0">{{ $rubric->title }}</dd>
          @if ($rubric->description)
            <dt class="col-3 text-right pl-0">Description</dt>
            <dd class="col-9 pl-0">{{ $rubric->description }}</dd>
          @endif
            <dt class="col-3 text-right pl-0">Position</dt>
            <dd class="col-9 pl-0">{{ AP::getRubricPosition($rubric->position).' - '.$rubric->rank }}</dd>
          @if ($rubric->childs->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Sous-rubriques</dt>
            <ul>
              @foreach ($rubric->childs as $child)
                <li>{{ $child->name }}</li>
              @endforeach
            </ul>
          @endif
          @if ($rubric->posts->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Articles</dt>
            <ul>
              @foreach ($rubric->posts as $post)
                <li>{{ $post->title }}</li>
              @endforeach
            </ul>
          @endif
          @if ($rubric->groups->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Groupes rattachés à la rubrique</dt>
            <ul>
              @foreach ($rubric->groups as $group)
                <li>{{ $group->identity() }}</li>
              @endforeach
            </ul>
          @endif
          @if ($rubric->groupsWithRubricPostsRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Groupes ayant droits sur les articles de la rubrique</dt>
            <ul>
              @foreach ($rubric->groupsWithRubricPostsRight as $group)
                <li>{{ $group->name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $group->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $group->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($rubric->profilesWithRubricPostsRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Profils ayant droits sur les articles de la rubrique</dt>
            <ul>
              @foreach ($rubric->profilesWithRubricPostsRight as $profile)
                <li>{{ $profile->first_name }}</li>
                <dd class="col-12 px-0 mb-0">{{ $profile->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $profile->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($rubric->usersWithRubricPostsRight->isNotEmpty())
            <dt class="col-12 pl-0 mt-3">Utilisateurs ayant droits sur les articles de la rubrique</dt>
            <ul>
              @foreach ($rubric->usersWithRubricPostsRight as $user)
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
