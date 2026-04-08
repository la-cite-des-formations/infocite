@extends('layouts.modal')

@section('modal-size', 'modal-lg')
@section('modal-title')
    <span class="material-icons me-2">info</span> Rubrique « {{ $rubric->parent->name . ' > ' . $rubric->name }} »
@endsection

@section('modal-body')
    <div class="row g-3">

        {{-- ============  COLONNE GAUCHE : CONSULTATION  ============ --}}
        <div class="col-md-6">
            <div class="alert alert-primary h-100 mb-0">
                <h6 class="fw-bold mb-3">Consultation</h6>

                {{-- Groupes --}}
                @if ($readGroups->isNotEmpty())
                    <ul class="mb-0 small">
                        @foreach ($readGroups as $group)
                            <li class="fw-bold mb-1">{{ $group->public ?? $group->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="small fst-italic mb-0">Aucun groupe rattaché.</p>
                @endif

                {{-- Profils --}}
                @if ($readProfiles->isNotEmpty())
                    <p class="small mb-2 fw-bold text-uppercase border-bottom pb-1">Profils</p>
                    <div class="list-group list-group-flush mb-3 small">
                        @foreach ($readProfiles as $profile)
                            <div class="list-group-item px-0 py-2 bg-transparent border-0">
                                <div class="d-flex align-items-start">
                                    <span class="material-icons fs-6 me-2 mt-1">portrait</span>
                                    <div class="fw-bold">{{ $profile->first_name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Utilisateurs --}}
                @if ($readUsers->isNotEmpty())
                    <p class="small mb-2 fw-bold text-uppercase border-bottom pb-1">Utilisateurs particuliers</p>
                    <div class="list-group list-group-flush small">
                        @foreach ($readUsers as $user)
                            <div class="list-group-item px-0 py-2 bg-transparent border-0">
                                <div class="d-flex align-items-start">
                                    <span class="material-icons fs-6 me-2 mt-1">person</span>
                                    <div class="fw-bold">{{ $user->identity }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ============  COLONNE DROITE : ÉDITION  ============ --}}
        <div class="col-md-6 text-break">
            <div class="alert alert-success h-100 mb-0">
                <h6 class="fw-bold mb-3">Édition</h6>

                {{-- Utilisateurs --}}
                @if ($editUsers->isNotEmpty())
                    <p class="small mb-2 fw-bold text-uppercase border-bottom pb-1">Utilisateurs mandatés</p>
                    <ul class="mb-4 small">
                        @foreach ($editUsers as $user)
                            <li class="fw-bold mb-1">{{ $user->identity }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Groupes --}}
                @if ($editGroups->isNotEmpty())
                    <div class="mb-4">
                        @foreach ($editGroups as $group)
                            <p class="small mb-1 fw-bold text-uppercase border-bottom pb-1">
                                {{ $group->public ?? $group->name }}</p>
                            @if ($group->users->isNotEmpty())
                                <ul class="small mb-3">
                                    @foreach ($group->users as $user)
                                        <li>{{ $user->identity }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="small fst-italic mb-3">Aucun utilisateur.</p>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Profils --}}
                @if ($editProfiles->isNotEmpty())
                    <p class="small mb-2 fw-bold text-uppercase border-bottom pb-1">Autres profils concernés</p>
                    <ul class="mb-0 small">
                        @foreach ($editProfiles as $profile)
                            <li class="mb-1">
                                <span class="fw-bold">{{ $profile->first_name }}</span>
                                @if ($profile->users->isNotEmpty())
                                    <span
                                        class="fst-italic">({{ $profile->users->pluck('identity')->implode(', ') }})</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <style>
        .extra-small {
            font-size: 0.75rem;
        }
    </style>
@endsection
