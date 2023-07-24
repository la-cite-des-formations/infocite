<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="infos" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
            <p>{{ $rubric->description }}</p>
        </div>
        <div class="container row ms-auto me-auto">
            <div class="card rounded-pill d-flex flex-row flex-wrap text-wrap justify-content-around align-items-center infos-card"> {{-- ou rounded-4 --}}
                <div class="col-8 col-sm-3 col-lg col-xl-2 m-3 p-3 infos-div">
                    @if ($user->avatar)
                        <img src="{{asset('img')}}/{{$user->avatar}}" class="infos-img" />
                    @else
                        <img src="{{asset('img/dummy.png')}}" class="opacity-50 infos-img" />
                        {{-- <img src="{{asset('img/fou.png')}}" width="100%" /> --}}
                    @endif
                </div>

                <dl class="row col-12 col-sm-9 col-lg col-xl-4 infos-dl2 my-3">
                    <dt class="col-3 align-self-center text-end" >{{ $user->gender == 'F' ? 'Mme.' : 'M.' }}</dt>
                    <dd class="col-9 text-start">{{ "$user->first_name $user->name" }}
                        <span class="col material-icons text-end mt-2 ms-4">
                            {{ $user->is_staff ? 'corporate_fare' : 'school' }}
                        </span>
                    </dd>
                    @if(!empty($user->google_account))
                    <a class="col-12 alert-link align-self-center text-center mb-3" href="mailto:{{ $user->google_account }}" role="button">{{ $user->google_account }}</a>
                    @endif
                    @if(!empty($user->birthday))
                    <!-- Né(e) le __/__/____ -->
                    <dt class="col-6 col-sm-3 text-end">
                        {{ empty($user->gender) ? 'Naissance' : ($user->gender == 'M' ? 'Né le' : 'Née le') }}
                    </dt>
                    <dd class="col-6 col-sm-9 text-start">{{ $user->birthday->format('d/m/Y') }}</dd>
                    @endif
                    @if(!$user->is_staff)
                    <!-- Email _____@____.__ -->
                    <dt class="col-sm-3 text-end">Email</dt>
                    @if(empty($user->email))
                    <dd class="col-sm-9 pe-0 font-italic">non communiqué</dd>
                    @else
                    <a href="mailto:{{ $user->email }}" class="col-sm-9 text-start">{{ $user->email }}</a>
                    @endif
                    @endif
                    @if(count($user->profiles) > 0)
                    <dt class="col-6 col-sm-3 text-end">Status</dt>
                    <div class="col-6 col-sm-9 text-start">
                        @foreach($user->profiles as $profile)
                            <dd value="{{ $profile->id }}" class="col-12">
                                {{ $profile->first_name }}
                            </dd>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty($user->language))
                    <!-- Language -->
                    <dt class="col-6 col-sm-3 text-end">Langue</dt>
                    <dd class="col-6 col-sm-9 text-start">{{ $user->language }}</dd>
                    @endif
                    {{-- @if(!empty($user->status))
                    <!-- Statut __________  ...  -->
                    <dt class="col-3 align-self-center text-center">Statut</dt>
                    <dd class="col-{{ 2 + 7 * empty($user->quality)}} pe-0">{{ $user->status }}</dd>
                    @endif --}}
                    @if(!empty($user->quality))
                    <!-- ...  Qualité __________  -->
                    <dt class="col-6 col-sm-3 text-end">Qualité</dt>
                    {{-- <dd class="col-{{ 4 + 5 * empty($user->status) }} pe-0">{{ AP::getQuality($user->quality) }}</dd> --}}
                    <dd class="col-6 col-sm-9 text-start">{{ AP::getQuality($user->quality) }}</dd>
                    @endif
                </dl>
                <dl class="row col-md-11 col-xl">
                    @if(empty($user->code_ypareo))
                      <p class="col-12 text-start no-ypareo mt-5 pt-3">Utilisateur externe non répertorié dans YParéo.</p>
                    @else
                      <!-- Code YParéo _____  Code Net YParéo _____ -->
                      <dt class="text-end col-6">Code YParéo</dt>
                      <dd class="col-6 text-start">{{ $user->code_ypareo  }}</dd>
                      <dt class="col-6 text-end">Code Net YParéo</dt>
                      <dd class="col-6 text-start align-self-center @if(empty($user->code_netypareo)) font-italic @endif">{{ $user->code_netypareo ?: 'non généré' }}</dd>
                      <!-- Login YParéo _____ -->
                      <dt class="col-6 text-end">Login YParéo</dt>
                      <dd class="col-6 text-start @if(empty($user->login)) font-italic @endif">{{ $user->login ?: 'non généré' }}</dd>
                      <!-- Mdp YParéo _____ -->
                      <dt class="col-6 text-end">Mdp YParéo</dt>
                      <dd class="col-6 text-start @if(empty($user->ypareo_pwd)) font-italic @endif">{{ $user->ypareo_pwd ?: 'sécurisé' }}</dd>
                      <!-- N° Badge _____ -->
                      <dt class="col-6 text-end">N° Badge</dt>
                      <dd class="col-6 text-start @if(empty($user->badge)) font-italic @endif">{{ $user->badge ?: 'non défini' }}</dd>
                </dl>
                    @endif
                <dl class="row col-12 col-sm-10 col-md-11 col-xl text-wrap pe-3">
                    @if($user->groupsList(['P']))
                        <!-- Équipe _________  -->
                        <dt class="col-6 col-sm-3 text-end">{{ $user->groups(['P'])->count() > 1 ? 'Équipes' : 'Équipe'}}</dt>
                        {{-- <dd class="col-{{ 2 + 7 * empty($user->functionsList(['P']))}} pe-0"> --}}
                        <dd class="col-6 col-sm-6 text-start">
                            {{ $user->groupsList(['P']) }}
                        </dd>
                    @endif
                    @if($user->functionsList(['P']))
                        <!-- ...  Fonction __________  -->
                        <dt class="col-6 col-sm-3 text-end">Fonction</dt>
                        <dd class="col-6 col-sm-4 text-start">{{ $user->functionsList(['P']) }}</dd>
                    @endif
                    @if($user->groupsList(['C']) || $user->groupsList(['E']))
                        <!-- Classe(s) ______, ______, ... -->
                        <dt class="col-6 col-xl-3 text-start dt-class">{{ $user->groups(['C']) -> count() + $user->groups(['E']) -> count() == 1 ? 'Classe' : 'Classes'}}</dt>
                        <dd class="col-6 col-xl-6 text-start">{{ $user->groupsList(['C']) }}</dd>
                        <dd class="col-6 col-xl-6 text-start">{{ $user->groupsList(['E']) }}</dd>
                    @endif
                </dl>
                    {{-- <dt class="col-3 text-right pe-0">{{ $userNbClasses > 1 ? 'Classes' : 'Classe' }}</dt> --}}
                    {{-- <dd class="col-9 pe-0 @if($truncateClassesList) text-truncate @endif"
                    @if($userNbClasses > $classesMin)
                        wire:click="switchClasses" role="button"
                    @endif>{{ $user->groupsList(['C']) ?: $user->groupsList(['E']) }}</dd> --}}
            </div>
            <div class="container mt-5" @if ($firstLoad) data-aos="fade-up" @endif>
                <div class="row mb-3">
                    <h3 class="title-icon text-center mb-4"><i class="material-icons fs-2 me-2">collections</i>Mes articles favoris</h3>
                @foreach ($favorites as $i => $post)
                @can('view', $post)
                    <div class="col-sm-12 col-md-6 col-lg-3 d-flex align-items-stretch mt-2 mb-3"
                            @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 4 + 1) * 100 }}" @endif>
                        <div class="position-relative icon-box d-flex flex-column">
                            <!-- Titre de l'article -->
                            <h4>
                                <a href="{{ $post->rubric->route().'/'.$post->id }}">
                                    <!-- Icone -->
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="icon"><i class="material-icons">{{ $post->icon }}</i></div>
                                    @if(!$post->published)
                                        <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="non publié">unpublished</i>
                                    @endif
                                    @if($post->expired())
                                        <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="expiré">auto_delete</i>
                                    @endif
                                    @if($post->forthcoming())
                                        <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="à venir">schedule_send</i>
                                    @endif
                                    </div>
                                    <div>{{ $post->title }}</div>
                                </a>
                            </h4>
                            <!-- Sous Titre de l'article -->
                                <p>{!! $post->preview() !!}</p>
                            <!-- Boutons d'actions -->

                            <div class="align-self-end mt-auto">
                                <div class="input-group" role="group" aria-label="Actions">
                                @if($post->isCommentable())
                                    <!-- NB de commentaires déposés sur l'article : class info si au moins 1 commentaire  -->
                                    <div class="input-group-text btn-sm @if ($post->comments->count() > 0) btn-primary @else btn-secondary @endif"
                                        type="text" title="Commentaires">
                                    @if ($post->comments->count() > 0)
                                        <span class="me-1">{{ $post->comments->count() }}</span>
                                    @endif
                                        <i class="bx bx-comment-detail"></i>
                                    </div>
                                @endif
                                    <!-- Pour ajouter l'article aux favoris : class warning si deja ajouté aux favoris-->
                                    <button class="btn @if ($post->isFavorite()) btn-warning @else btn-secondary @endif btn-sm"
                                            title="@if ($post->isFavorite()) Retirer des favoris @else Ajouter aux favoris @endif"
                                            wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                        <i class="bx bx-star"></i>
                                    </button>
                                    <!-- Article deja lu ? : class success si deja lu -->
                                    <div class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                                            type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                                        <i class="bx bx-message-alt-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @endforeach
                </div>
                @include('includes.pagination', ['elements' => $favorites])
            </div>
        </div>
    </section>
</div>
