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
            <dt class="col-3 text-end ps-0 mt-3">Système de notation</dt>
            <dd class="col-9 ps-0 mt-3">
                @if ($post->is_rating_enabled)
                    <span class="text-success fw-bold">Activé</span>
                    @php $avg = $post->averageRating(); @endphp
                    @if ($avg > 0)
                        <span class="ms-2">(Note moyenne : <strong>{{ number_format($avg, 1) }}</strong> / 5)</span>
                    @else
                        <span class="ms-2 fst-italic text-muted">(Aucun avis pour le moment)</span>
                    @endif
                @else
                    <span class="text-danger fw-bold">Désactivé</span>
                @endif
            </dd>
            <dt class="col-3 text-end ps-0 mt-3">Acquittement de lecture</dt>
            <dd class="col-9 ps-0 mt-3">
                @if ($post->is_acknowledgment_required)
                    <span class="text-success fw-bold text-uppercase">Exigé</span>
                    @php $acknowledgers = $post->acknowledgers()->orderByPivot('occurred_at', 'desc')->get(); @endphp
                    @if ($acknowledgers->isNotEmpty())
                        <div class="mt-2 card bg-light border-0 shadow-sm p-2" style="max-height: 200px; overflow-y: auto;">
                             <h6 class="small fw-bold text-primary mb-2"><span class="material-icons-outlined align-middle md-18 me-1">how_to_reg</span> Utilisateurs ayant acquitté ({{ $acknowledgers->count() }}) :</h6>
                             <ul class="list-unstyled mb-0 ms-1">
                                @foreach ($acknowledgers as $user)
                                    <li class="small mb-1 pb-1 border-bottom border-white">
                                        <span class="material-icons-outlined align-middle md-14 text-secondary me-1">{{ $user->is_staff ? 'badge' : 'school' }}</span>
                                        <strong>{{ $user->identity }}</strong>
                                        <span class="text-muted italic"> - le {{ \Carbon\Carbon::parse($user->pivot->occurred_at)->format('d/m/Y à H:i') }}</span>
                                    </li>
                                @endforeach
                             </ul>
                        </div>
                    @else
                        <div class="mt-1 small fst-italic text-muted">
                            <span class="material-icons-outlined align-middle md-18 me-1">info</span> Aucun acquittement enregistré pour le moment.
                        </div>
                    @endif
                @else
                    <span class="text-secondary fw-bold italic">Pas d'accusé de réception demandé</span>
                @endif
            </dd>
          @if ($post->gallery && count($post->gallery->images) > 0)
            <dt class="col-3 text-end ps-0 mt-3">Galerie Photos</dt>
            <dd class="col-9 ps-0 mt-3">{{ count($post->gallery->images) }} photo(s)</dd>
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
