<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="infos" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
            <p>{{ $rubric->description }}</p>
        </div>
        <div class="container row col-8 ms-auto me-auto">
            <div class="card rounded-pill d-flex flex-row justify-content-around align-items-center infos-card"> {{-- ou rounded-4 --}}
                <div class="col-md-2 m-5 infos-img">
                    @if ($user->avatar)
                        <img src="{{asset('img')}}/{{$user->avatar}}" width="100%" />
                    @else
                        <img src="{{asset('img/dummy.png')}}" width="100%" class="opacity-50" />
                        {{-- <img src="{{asset('img/fou.png')}}" width="100%" /> --}}
                    @endif
                </div>
                {{-- <div class="col-md-4 m-5 pt-5">
                    <p>{{ $user->gender == 'F' ? 'Mme.' : 'M.' }} {{ $user->name }}</p>
                    <p>Prénom : {{ $user->first_name }}</p>
                    <p>E-mail : {{ $user->email }}</p>

                    @foreach($user->profiles as $profile)
                        <option value="{{ $profile->id }}">
                            {{ $profile->first_name }}
                        </option>
                    @endforeach
                </div> --}}

                <dl class="position-relative py-3 ps-3 pe-5 me-3">
                    <h5>{{ $user->gender == 'F' ? 'Mme.' : 'M.' }} {{ "$user->first_name $user->name" }}
                        <span class="material-icons mt-2 ps-2">
                            {{ $user->is_staff ? 'corporate_fare' : 'school' }}
                        </span>
                    </h5>
                      @if(!empty($user->google_account))
                        <a class="alert-link" href="mailto:{{ $user->google_account }}" role="button">{{ $user->google_account }}</a>
                      @endif
                      @if(!empty($user->birthday))
                        <!-- Né(e) le __/__/____ -->
                        <dt class="col-3 text-right pe-0">
                            {{ empty($user->gender) ? 'Naissance' : ($user->gender == 'M' ? 'Né le' : 'Née le') }}
                        </dt>
                        <dd class="col-9 pe-0">{{ $user->birthday->format('d/m/Y') }}</dd>
                      @endif
                      {{-- @if(!$user->is_staff)
                        <!-- Email _____@____.__ -->
                        <dt class="col-3 text-right pe-0">Email</dt>
                       @if(empty($user->email))
                        <dd class="col-9 pe-0 font-italic">non communiqué</dd>
                       @else
                        <dd class="col-9 pe-0"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></dd>
                       @endif
                      @endif --}}
                      @if($user->email)
                        <!-- Email _____@____.__ -->
                            <dt class="col text-right">Email</dt>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        @else
                            <dd class="col-9 pe-0 font-italic">Aucun mail personnel</dd>
                      @endif
                      @if(count($user->profiles) > 0)
                        <dt>Status</dt>
                        @foreach($user->profiles as $profile)
                            <dd value="{{ $profile->id }}">
                                {{ $profile->first_name }}
                            </dd>
                        @endforeach
                      @endif
                </dl>
                    @if(!empty($user->status))
                  <!-- Statut __________  ...  -->
                  <dt class="col-3 text-right pe-0">Statut</dt>
                  <dd class="col-{{ 2 + 7 * empty($user->quality)}} pe-0">{{ $user->status }}</dd>
                @endif
                @if(!empty($user->quality))
                  <!-- ...  Qualité __________  -->
                  <dt class="col-3 text-right">Qualité</dt>
                  <dd class="col-{{ 4 + 5 * empty($user->status) }} pe-0">{{ AP::getQuality($user->quality) }}</dd>
                @endif
                <dl class="row mb-0 mx-0 flex-wrap">
                    @if($user->groupsList(['P']))
                      <!-- Équipe _________  -->
                      <dt class="col-3 text-right pe-0">{{ $user->groups(['P'])->count() > 1 ? 'Équipes' : 'Équipe'}}</dt>
                      <dd class="col-{{ 2 + 7 * empty($user->functionsList(['P']))}} pe-0">
                          {{ $user->groupsList(['P']) }}
                      </dd>
                    @endif
                    @if($user->functionsList(['P']))
                      <!-- ...  Fonction __________  -->
                      <dt class="col-3 text-right">Fonction</dt>
                      <dd class="col-4 pe-0">{{ $user->functionsList(['P']) }}</dd>
                    @endif
                    {{-- @if($user->groupsList(['C']) || $user->groupsList(['E']))
                      <!-- Classe(s) ______, ______, ... -->
                      <dt class="col-3 text-right pe-0">{{ $userNbClasses > 1 ? 'Classes' : 'Classe' }}</dt>
                      <dd class="col-9 pe-0 @if($truncateClassesList) text-truncate @endif"
                        @if($userNbClasses > $classesMin)
                          wire:click="switchClasses" role="button"
                        @endif>{{ $user->groupsList(['C']) ?: $user->groupsList(['E']) }}</dd>
                    @endif --}}
                    @if(empty($user->code_ypareo))
                      <p class="col-12">Utilisateur externe non répertorié dans YParéo.</p>
                    @else
                      <!-- Code YParéo _____  Code Net YParéo _____ -->
                      <dt class="col-3 text-right pe-0">Code YParéo</dt>
                      <dd class="col-2 pe-0">{{ $user->code_ypareo  }}</dd>
                      <dt class="col-4 text-right pe-0">Code Net YParéo</dt>
                      <dd class="col-3 pe-0 @if(empty($user->code_netypareo)) font-italic @endif">{{ $user->code_netypareo ?: 'non généré' }}</dd>
                      <!-- Login YParéo _____ -->
                      <dt class="col-3 text-right pe-0">Login YParéo</dt>
                      <dd class="col-9 pe-0 @if(empty($user->login)) font-italic @endif">{{ $user->login ?: 'non généré' }}</dd>
                      <!-- Mdp YParéo _____ -->
                      <dt class="col-3 text-right pe-0">Mdp YParéo</dt>
                      <dd class="col-9 pe-0 @if(empty($user->ypareo_pwd)) font-italic @endif">{{ $user->ypareo_pwd ?: 'sécurisé' }}</dd>
                      <!-- N° Badge _____ -->
                      <dt class="col-3 text-right pe-0">N° Badge</dt>
                      <dd class="col-9 pe-0 @if(empty($user->badge)) font-italic @endif">{{ $user->badge ?: 'non défini' }}</dd>
                    @endif
                  </dl>
            </div>
        </div>
    </section>
</div>
