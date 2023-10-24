@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    <div class="alert @if($user->is_frozen) alert-warning @else alert-success @endif mb-3">
        <div class="d-flex">
            <div class="my-auto mr-3">
              @if($user->avatar)
                <img class="img-thumbnail rounded float-left" src="{{ $user->avatar }}">
              @else
                <span class="material-icons md-36">person</span>
              @endif
            </div>
            <div class="my-auto">
                <h5>{{ "$user->first_name $user->name" }}</h5>
              @if(!empty($user->google_account))
                <a class="alert-link" href="mailto:{{ $user->google_account }}" role="button">{{ $user->google_account }}</a>
              @endif
            </div>
            <div class="ml-auto my-auto">
                <span class="material-icons-outlined">@if($user->is_staff) corporate_fare @else school @endif</span>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
          @if(!empty($user->birthday))
            <!-- Né(e) le __/__/____ -->
            <dt class="col-3 text-right pl-0">
                {{ empty($user->gender) ? 'Naissance' : ($user->gender == 'M' ? 'Né le' : 'Née le') }}
            </dt>
            <dd class="col-9 pl-0">{{ $user->birthday->format('d/m/Y') }}</dd>
          @endif
          @if(!empty($user->status))
            <!-- Statut __________  ...  -->
            <dt class="col-3 text-right pl-0">Statut</dt>
            <dd class="col-{{ 2 + 7 * empty($user->quality)}} pl-0">{{ $user->status }}</dd>
          @endif
          @if(!empty($user->quality))
            <!-- ...  Qualité __________  -->
            <dt class="col-3 text-right">Qualité</dt>
            <dd class="col-{{ 4 + 5 * empty($user->status) }} pl-0">{{ AP::getQuality($user->quality) }}</dd>
          @endif
          @if($user->groupsList(['P']))
            <!-- Équipe _________  -->
            <dt class="col-3 text-right pl-0">{{ $user->groups(['P'])->count() > 1 ? 'Équipes' : 'Équipe'}}</dt>
            <dd class="col-{{ 2 + 7 * empty($user->functionsList(['P']))}} pl-0">
                {{ $user->groupsList(['P']) }}
            </dd>
          @endif
          @if($user->functionsList(['P']))
            <!-- ...  Fonction __________  -->
            <dt class="col-3 text-right">Fonction</dt>
            <dd class="col-4 pl-0">{{ $user->functionsList(['P']) }}</dd>
          @endif
          @if($user->groupsList(['C']) || $user->groupsList(['E']))
            <!-- Classe(s) ______, ______, ... -->
            <dt class="col-3 text-right pl-0">{{ $userNbClasses > 1 ? 'Classes' : 'Classe' }}</dt>
            <dd class="col-9 pl-0 @if($truncateClassesList) text-truncate @endif"
              @if($userNbClasses > $classesMin)
                wire:click="switchClasses" role="button"
              @endif>{{ $user->groupsList(['C']) ?: $user->groupsList(['E']) }}</dd>
          @endif
          @if(empty($user->code_ypareo))
            <p class="col-12">Utilisateur externe non répertorié dans YParéo.</p>
          @else
            <!-- Code YParéo _____  Code Net YParéo _____ -->
            <dt class="col-3 text-right pl-0">Code YParéo</dt>
            <dd class="col-2 pl-0">{{ $user->code_ypareo  }}</dd>
            <dt class="col-4 text-right pl-0">Code Net YParéo</dt>
            <dd class="col-3 pl-0 @if(empty($user->code_netypareo)) font-italic @endif">{{ $user->code_netypareo ?: 'non généré' }}</dd>
            <!-- Login YParéo _____ -->
            <dt class="col-3 text-right pl-0">Login YParéo</dt>
            <dd class="col-9 pl-0 @if(empty($user->login)) font-italic @endif">{{ $user->login ?: 'non généré' }}</dd>
            <!-- Mdp YParéo _____ -->
            <dt class="col-3 text-right pl-0">Mdp YParéo</dt>
            <dd class="col-9 pl-0 @if(empty($user->ypareo_pwd)) font-italic @endif">{{ $user->ypareo_pwd ?: 'sécurisé' }}</dd>
            <!-- N° Badge _____ -->
            <dt class="col-3 text-right pl-0">N° Badge</dt>
            <dd class="col-9 pl-0 @if(empty($user->badge)) font-italic @endif">{{ $user->badge ?: 'non défini' }}</dd>
          @endif
          @if(!$user->is_staff)
            <!-- Email _____@____.__ -->
            <dt class="col-3 text-right pl-0">Email</dt>
           @if(empty($user->email))
            <dd class="col-9 pl-0 font-italic">non communiqué</dd>
           @else
            <dd class="col-9 pl-0"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></dd>
           @endif
          @endif
          @if ($user->groups(['S'])->get()->count())
            <dt class="col-12 pl-0 mt-2">Groupes Système</dt>
            <ul>
                @foreach ($user->groups(['S'])->get() as $group)
                  <li>{{ $group->name }}</li>
                @endforeach
            </ul>
          @endif
          @if ($user->groups(['A'])->get()->count())
            <dt class="col-12 pl-0 mt-2">Autres groupes</dt>
            <ul>
                @foreach ($user->groups(['A'])->get() as $group)
                  <li>{{ $group->name }}</li>
                @endforeach
            </ul>
          @endif
          @if ($user->personalRights->count())
            <dt class="col-12 pl-0 mt-2">Droits personnels</dt>
            <ul>
              @foreach ($user->personalRights as $right)
                <li>{{ $right->description.$right->rightsResourceableString() }}</li>
                <dd class="col-12 px-0 mb-0">{{ $right->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $right->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($user->profilesRights()->count())
            <dt class="col-12 pl-0 mt-2">Droits par profil</dt>
            <ul>
              @foreach ($user->profilesRights()->sortBy('name')->sortByDesc('pivot.priority')->groupBy('name') as $right)
                <li>{{
                    $user->profiles->firstWhere('id', $right->first()->pivot->rightable_id)->first_name.' - '.
                    $right->first()->description.$right->first()->rightsResourceableString()
                }}</li>
                <dd class="col-12 px-0 mb-0">{{ $right->first()->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $right->first()->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
          @if ($user->groupsRights()->count())
            <dt class="col-12 pl-0 mt-2">Droits par groupe</dt>
            <ul>
              @foreach ($user->groupsRights()->sortBy('name')->sortByDesc('pivot.priority')->groupBy('name') as $right)
                <li>{{
                    $user->groups->firstWhere('id', $right->first()->pivot->rightable_id)->identity().' - '.
                    $right->first()->description.$right->first()->rightsResourceableString()
                }}</li>
                <dd class="col-12 px-0 mb-0">{{ $right->first()->getRightableRoles() }}</dd>
                <dd class="col-12 px-0 font-italic">Ordre de priorité : {{ $right->first()->pivot->priority }}</dd>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
