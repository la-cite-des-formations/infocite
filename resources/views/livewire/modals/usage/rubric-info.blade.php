@extends('layouts.modal')

@section('modal-size', 'modal-lg')
@section('modal-title')
    <span class="material-icons me-2">info</span> Droits sur la rubrique « {{ $rubric->name }} »
@endsection

@section('modal-body')
<div class="row">

    {{-- ============  COLONNE GAUCHE : CONSULTATION  ============ --}}
    <div class="col-md-6 border-end">
        <h6 class="text-primary fw-bold mb-3">
            <span class="material-icons align-middle fs-5 me-1">visibility</span>
            Consultation
        </h6>

        {{-- Groupes --}}
        <p class="text-muted small mb-1 fw-semibold">Groupes</p>
        @if($readGroups->isNotEmpty())
            <ul class="list-group list-group-flush mb-3">
                @foreach($readGroups as $group)
                    <li class="list-group-item px-0 py-1 bg-transparent border-0">
                        &bull; {{ $group->name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small fst-italic mb-3">Aucun groupe.</p>
        @endif

        {{-- Profils --}}
        <p class="text-muted small mb-1 fw-semibold">Profils</p>
        @if($readProfiles->isNotEmpty())
            <ul class="list-group list-group-flush mb-3">
                @foreach($readProfiles as $profile)
                    <li class="list-group-item px-0 py-1 bg-transparent border-0">
                        &bull; {{ $profile->first_name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small fst-italic mb-3">Aucun profil.</p>
        @endif
    </div>

    {{-- ============  COLONNE DROITE : ÉDITION  ============ --}}
    <div class="col-md-6">
        <h6 class="text-success fw-bold mb-3">
            <span class="material-icons align-middle fs-5 me-1">edit</span>
            Édition (articles)
        </h6>

        {{-- Groupes éditeurs --}}
        <p class="text-muted small mb-1 fw-semibold">Groupes</p>
        @if($editGroups->isNotEmpty())
            <ul class="list-group list-group-flush mb-3">
                @foreach($editGroups as $group)
                    <li class="list-group-item px-0 py-1 bg-transparent border-0">
                        &bull; {{ $group->name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small fst-italic mb-3">Aucun groupe éditeur.</p>
        @endif

        {{-- Profils éditeurs --}}
        <p class="text-muted small mb-1 fw-semibold">Profils</p>
        @if($editProfiles->isNotEmpty())
            <ul class="list-group list-group-flush mb-3">
                @foreach($editProfiles as $profile)
                    <li class="list-group-item px-0 py-1 bg-transparent border-0">
                        &bull; {{ $profile->first_name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small fst-italic mb-3">Aucun profil éditeur.</p>
        @endif
    </div>
</div>
@endsection
