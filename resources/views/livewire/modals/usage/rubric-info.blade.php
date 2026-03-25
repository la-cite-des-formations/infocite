@extends('layouts.modal')

@section('modal-size', 'modal-lg')
@section('modal-title')
    <span class="material-icons me-2">info</span> Droits sur la rubrique « {{ $rubric->name }} »
@endsection

@section('modal-body')
<div class="row">

    {{-- ============  COLONNE GAUCHE : CONSULTATION  ============ --}}
    <div class="col-md-5 border-end">
        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
            <span class="material-icons fs-5 me-2">visibility</span>
            Consultat° (groupes & pers.)
        </h6>

        {{-- Groupes --}}
        <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Groupes</p>
        @if($readGroups->isNotEmpty())
            <div class="list-group list-group-flush mb-3 small">
                @foreach($readGroups as $group)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex align-items-start">
                            <span class="material-icons text-muted fs-6 me-2 mt-1">groups</span>
                            <div>
                                <div class="fw-bold text-dark">{{ $group->name }}</div>
                                @if(isset($group->pivot) && is_null($group->pivot->resource_id))
                                    <div class="text-info extra-small"><i class="bx bx-world"></i> Droit générique</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small fst-italic mb-3 font-italic">Aucun groupe rattaché.</p>
        @endif

        {{-- Profils (si droits spécifiques) --}}
        @if($readProfiles->isNotEmpty())
            <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Profils</p>
            <div class="list-group list-group-flush mb-3 small">
                @foreach($readProfiles as $profile)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex align-items-start">
                            <span class="material-icons text-muted fs-6 me-2 mt-1">portrait</span>
                            <div class="fw-bold">{{ $profile->first_name }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Utilisateurs (si droits spécifiques) --}}
        @if($readUsers->isNotEmpty())
            <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Utilisateurs</p>
            <div class="list-group list-group-flush small">
                @foreach($readUsers as $user)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex align-items-start">
                            <span class="material-icons text-muted fs-6 me-2 mt-1">person</span>
                            <div class="fw-bold">{{ $user->identity }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============  COLONNE DROITE : ÉDITION  ============ --}}
    <div class="col-md-7 ps-4 text-break">
        <h6 class="text-success fw-bold mb-3 d-flex align-items-center">
            <span class="material-icons fs-5 me-2">edit</span>
            Édition (groupes & pers.)
        </h6>

        {{-- Groupes --}}
        <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Groupes</p>
        @if($editGroups->isNotEmpty())
            <div class="list-group list-group-flush mb-4 small">
                @foreach($editGroups as $group)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <span class="material-icons text-muted fs-6 me-2 mt-1">groups</span>
                                <div>
                                    <div class="fw-bold">{{ $group->name }}</div>
                                    <div class="text-muted mt-1">
                                        Rôles : <span class="badge bg-light text-dark border">{{ $group->getRightableRoles() }}</span>
                                    </div>
                                    @if(is_null($group->pivot->resource_id))
                                        <div class="text-info extra-small mt-1"><i class="bx bx-world"></i> Droit générique (global)</div>
                                    @elseif($group->pivot->resource_id == $rubric->parent_id)
                                        <div class="text-muted extra-small mt-1"><i class="bx bx-subdirectory-right"></i> Hérité du parent : {{ $rubric->parent->name }}</div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-secondary opacity-75" title="Priorité">P{{ $group->pivot->priority }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small fst-italic mb-4">Aucun groupe éditeur.</p>
        @endif

        {{-- Profils --}}
        <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Profils/Métiers</p>
        @if($editProfiles->isNotEmpty())
            <div class="list-group list-group-flush mb-4 small">
                @foreach($editProfiles as $profile)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <span class="material-icons text-muted fs-6 me-2 mt-1">portrait</span>
                                <div>
                                    <div class="fw-bold">{{ $profile->first_name }}</div>
                                    <div class="text-muted mt-1">
                                        Rôles : <span class="badge bg-light text-dark border text-wrap">{{ $profile->getRightableRoles() }}</span>
                                    </div>
                                    @if(is_null($profile->pivot->resource_id))
                                        <div class="text-info extra-small mt-1"><i class="bx bx-world"></i> Droit générique (global)</div>
                                    @elseif($profile->pivot->resource_id == $rubric->parent_id)
                                        <div class="text-muted extra-small mt-1"><i class="bx bx-subdirectory-right"></i> Hérité du parent : {{ $rubric->parent->name }}</div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-secondary opacity-75" title="Priorité">P{{ $profile->pivot->priority }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small fst-italic mb-4">Aucun profil éditeur.</p>
        @endif

        {{-- Utilisateurs --}}
        <p class="text-muted small mb-2 fw-bold text-uppercase border-bottom pb-1">Individus</p>
        @if($editUsers->isNotEmpty())
            <div class="list-group list-group-flush small">
                @foreach($editUsers as $user)
                    <div class="list-group-item px-0 py-2 bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <span class="material-icons text-muted fs-6 me-2 mt-1">person</span>
                                <div>
                                    <div class="fw-bold">{{ $user->identity }}</div>
                                    <div class="text-muted mt-1">
                                        Rôles : <span class="badge bg-light text-dark border">{{ $user->getRightableRoles() }}</span>
                                    </div>
                                    @if(is_null($user->pivot->resource_id))
                                        <div class="text-info extra-small mt-1"><i class="bx bx-world"></i> Droit générique (global)</div>
                                    @elseif($user->pivot->resource_id == $rubric->parent_id)
                                        <div class="text-muted extra-small mt-1"><i class="bx bx-subdirectory-right"></i> Hérité du parent : {{ $rubric->parent->name }}</div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-secondary opacity-75" title="Priorité">P{{ $user->pivot->priority }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small fst-italic">Aucun utilisateur éditeur direct.</p>
        @endif
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .list-group-item:not(:last-child) { border-bottom: 1px dashed #eee !important; }
</style>
@endsection
