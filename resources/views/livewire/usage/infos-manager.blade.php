<section wire:key='infosView' id="infos" class="services section-bg">
  @can('edit', ['App\\Models\\Post', $rubric->id])
    <div class="container d-flex flex-column">
        <div class="align-self-end">
            <button class="btn btn-sm btn-primary" wire:click='switchMode' type="button"
                    title="@if ($mode == 'view') Passer en mode édition @else Passer en mode lecture @endif">
                <span class="bx @if ($mode == 'view') bx-pencil @else bx-show @endif"></span>
            </button>
        </div>
    </div>
  @endcan
    <div class="section-title">
        <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
        <p>{{ $rubric->description }}</p>
    </div>
    <div class="container d-flex flex-column align-items-center m-auto">
        <div class="card rounded-pill d-flex flex-row flex-wrap text-wrap infos-card py-5 pl-2">
            <div class="col-12 infos-div mb-4">
                <div class="d-flex justify-content-center my-auto ml-auto">
                    <div class="my-auto me-3">
                      @if($user->avatar)
                        <img class="img-thumbnail rounded float-left" src="{{ $user->avatar }}">
                      @else
                        <span class="material-icons md-36">person</span>
                      @endif
                    </div>
                    <div class="my-auto">
                        <h5>{{ "$user->identity" }}</h5>
                      @if(!empty($user->google_account))
                        <a class="alert-link" href="mailto:{{ $user->google_account }}" role="button">{{ $user->google_account }}</a>
                      @endif
                    </div>
                    <div class="ml-auto my-auto">
                        <span class="material-icons-outlined ms-1">@if($employee) corporate_fare @else school @endif</span>
                    </div>
                </div>
              @if (empty($user->code_ypareo))
                <p class="col-12 text-center fst-italic mt-2">Utilisateur externe non répertorié dans YParéo</p>
              @endif
            </div>
            <dl class="row col-xl ps-3 pe-1 mb-auto">
              @if(!empty($user->learner?->birthday))
                <!-- Né(e) le __/__/____ -->
                <dt class="col-6 text-end">
                    {{ empty($user->learner?->gender) ? 'Naissance' : ($user->learner?->gender == 'M' ? 'Né le' : 'Née le') }}
                </dt>
                <dd class="col-6 text-start">{{ $user->learner?->birthday->format('d/m/Y') }}</dd>
              @endif
              @if(!empty($user->learner?->language))
                <!-- Language -->
                <dt class="col-6 text-end">Langue</dt>
                <dd class="col-6 text-start">{{ $user->learner?->language }}</dd>
              @endif
              @if(! $employee)
                <!-- Email _____@____.__ -->
                <dt class="col-6 text-end">Email</dt>
               @if(empty($user->email))
                <dd class="col-6 font-italic">non communiqué</dd>
               @else
                <a href="mailto:{{ $user->email }}" class="col-6 text-start">{{ $user->email }}</a>
               @endif
              @endif
              @if(!empty($user->learner?->quality))
                <!-- ...  Qualité __________  -->
                <dt class="col-6 col-sm-3 text-end">Qualité</dt>
                <dd class="col-6 col-sm-9 text-start">{{ AP::getQuality($user->learner?->quality) }}</dd>
              @endif
              @if(count($user->profiles) > 0)
                <dt class="col-6 text-end">Profil</dt>
                <div class="col-6 text-start">
                  @foreach($user->profiles as $profile)
                    <dd class="col-12">
                        {{ $profile->first_name }}
                    </dd>
                  @endforeach
                </div>
              @endif
            </dl>
          @if(!empty($user->code_ypareo))
            <dl class="row col-xl px-1 mb-auto">
                <!-- Code YParéo _____  Code Net YParéo _____ -->
                <dt class="col-6 text-end">Code YParéo</dt>
                <dd class="col-6 text-start">{{ $user->code_ypareo  }}</dd>
                <dt class="col-6 text-end">Code Net YParéo</dt>
                <dd class="col-6 text-start @if(empty($user->code_netypareo)) font-italic @endif">{{ $user->code_netypareo ?: 'non généré' }}</dd>
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
            <dl class="row col-xl ps-1 pe-3 mb-auto">
              @if($user->groupsList(['E']))
                <!-- Équipe _________  -->
                <dt class="col-6 col-sm-4 text-end dt-class">{{ $user->groups(['E'])->count() > 1 ? 'Équipes' : 'Équipe'}}</dt>
                <dd class="col-6 col-sm-8 text-start">{{ $user->groupsList(['E']) }}</dd>
              @endif
                <!-- Fonction __________  -->
                <dt class="col-6 col-sm-4 text-end">Fonction</dt>
              @if($user->functionsList(['E']))
                <dd class="col-6 col-sm-8 text-start">{{ $user->functionsList(['E']) }}</dd>
              @elseif($employee?->position)
                <dd class="col-6 col-sm-8 text-start">{{ $employee->position }}</dd>
              @endif
              @if($user->groupsList(['C']) || $user->groupsList(['F']))
                <!-- Classe(s) ______, ______, ... -->
                <dt class="col-6 col-sm-4 text-end dt-class">{{ $user->groups(['C']) -> count() + $user->groups(['F']) -> count() == 1 ? 'Classe' : 'Classes'}}</dt>
                <dd class="col-6 col-sm-8 text-start">{{ implode('; ', [$user->groupsList(['C']), $user->groupsList(['F'])]) }}</dd>
              @endif
              @if($employee?->building)
                <!-- Bâtiment _______ -->
                <dt class="col-6 col-sm-4 text-end dt-class">Bâtiment</dt>
                <dd class="col-6 col-sm-8 text-start">{{ $employee->building }}</dd>
              @endif
              @if($employee?->office)
                <!-- Bâtiment _______ -->
                <dt class="col-6 col-sm-4 text-end dt-class">Bureau</dt>
                <dd class="col-6 col-sm-8 text-start">{{ $employee->office }}</dd>
              @endif
                <!-- Téléphones -->
              @if($phone = $user?->phone('internal'))
                <!-- Tél. interne _______ -->
                <dt class="col-6 col-sm-4 text-end dt-class">Tél. interne</dt>
                <dd class="col-6 col-sm-8 text-start @if(empty($phone->number)) fst-italic @endif">{{ $phone->number ?: 'non renseigné' }}</dd>
              @endif
              @if($phone = $user?->phone('mobile'))
                <!-- Tél. interne _______ -->
                <dt class="col-6 col-sm-4 text-end dt-class">Tél. mobile</dt>
                <dd class="col-6 col-sm-8 text-start @if(empty($phone->number)) fst-italic @endif">{{ $phone->number ?: 'non renseigné' }}</dd>
              @endif
              @if($phone = $user?->phone('external'))
                <!-- Tél. interne _______ -->
                <dt class="col-6 col-sm-4 text-end dt-class">Tél. externe</dt>
                <dd class="col-6 col-sm-8 text-start @if(empty($phone->number)) fst-italic @endif">{{ $phone->number ?: 'non renseigné' }}</dd>
              @endif
            </dl>
        </div>
      @if($employee)
        <div class="mt-4">
            <div class="form-check form-switch">
                <input wire:model='employee.desktop_notifications_granted' @if($browserDesktopNotificationsDenied) disabled @endif
                       class="form-check-input" type="checkbox" role="switch" id="switchDesktopNotificationGranted"/>
                <label for="switchDesktopNotificationGranted">Accepter les notifications de bureau</label>
            </div>
          @if ($employee->desktop_notifications_granted)
            <div class="ms-5">
                <div class="form-check">
                    <input wire:model='employee.notify_only_favorites' value=0
                           class="form-check-input" type="radio" id="radioAllNotifications">
                    <label class="form-check-label" for="radioAllNotifications">
                        Toutes les notifications
                    </label>
                  </div>
                  <div class="form-check">
                    <input wire:model='employee.notify_only_favorites' value=1
                           class="form-check-input" type="radio" id="radioOnlyFavoritesNotifications">
                    <label class="form-check-label" for="radioOnlyFavoritesNotifications">
                        Seulement pour les favoris
                    </label>
                </div>
            </div>
          @endif
        </div>
       @if ($browserDesktopNotificationsDenied)
        <div class="alert alert-danger col-9 mt-2" role="alert">
            <p>
                Impossible d'activer les notifications de bureau ! Pour ce faire, autorisez-les
                dans votre navigateur (si besoin, consultez la vidéo).
            </p>
            <h6>Guide : Autoriser manuellement les notifications avec Chrome</h6>
            <video src="{{ asset('img/unblock_notifications_guide.mp4') }}" controls
                    width="940" type="video/mp4" poster="{{ asset('img/unblock_notifications_poster.png') }}">
                    Débloquer les notifications sur Chrome
            </video>
        </div>
       @endif
      @endif
    </div>

    <!-- Mes rubriques favoris -->
    <div class="container mt-5" @if ($firstLoad) data-aos="fade-up" @endif>
        <h3 class="title-icon text-center mb-4"><i class="material-icons fs-2 me-2">category</i>Mes rubriques favoris</h3>
      @if ($user->favoriteRubrics->isEmpty())
        <p class="text-center">Aucune rubrique dans les favoris</p>
      @else
       @foreach ($user->favoriteRubrics as $i => $rubric)
        <div class="column aos-init aos-animate mt-2 mb-3"
             @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 20 + 1) * 100 }}" @endif>
            <div class="rubrics-div">
                <p class="fs-5 text-center">
                    <a href="{{ route('rubric.index', ['rubric' => $rubric->route()]) }}">{{ $rubric->name }}</a>
                    <button class="btn @if ($rubric->isFavorite) btn-warning @else btn-secondary @endif btn-sm"
                            title="@if ($rubric->isFavorite) Retirer des favoris @else Ajouter aux favoris @endif"
                            wire:click="switchFavoriteRubric({{ $rubric->id }})" type="button">
                        <i class="bx bx-star"></i>
                    </button>
                </p>
            </div>
        </div>
       @endforeach
      @endif
    </div>

    <!-- Mes articles favoris -->
    <div class="container mt-5" @if ($firstLoad) data-aos="fade-up" @endif>
        <div class="row mb-3">
            <h3 class="title-icon text-center mb-4"><i class="material-icons fs-2 me-2">collections</i>Mes articles favoris</h3>
         @if ($favoritesPosts->total() == 0)
            <p class="text-center">Aucun article dans les favoris</p>
         @else
          @foreach ($favoritesPosts as $i => $post)
           @can('read', $post)
            <div wire:click='redirectToPost({{ $post->id }})' role="button"
                 class="col-sm-12 col-md-4 col-lg-2 d-flex align-items-stretch mt-2 mb-3"
                 @if ($firstLoad) data-aos="zoom-in" data-aos-delay="{{ ($i  % 6 + 1) * 100 }}" @endif>
                <div class="position-relative icon-box d-flex flex-column">
                  @if (!$post->released && is_object($post->status))
                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger"
                       title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                  @endif
                    <!-- Titre de l'article -->
                    <h4>
                        <!-- Icone -->
                        <div class="icon"><i class="material-icons">{{ $post->icon }}</i></div>
                        <p class="m-1 fs-6"><i>Rubrique : {{ $post->rubric->name }}</i></p>
                        <a>{{ $post->title }}</a>
                    </h4>

                    <!-- Sous Titre de l'article -->
                    <p>{!! $post->preview() !!}</p>

                    <!-- Boutons d'actions -->
                    <div wire:click.prefetch='blockRedirection' class="align-self-end mt-auto">
                        <div class="input-group" role="group" aria-label="Actions">
                          @if ($mode == 'edition')
                           @can('update', $post)
                            <a href="{{ route('post.edit', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}"
                               role="button" class="btn btn-sm btn-success" title="Modifier">
                                <i class="bx bx-pencil"></i>
                            </a>
                           @endcan
                          @endif
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
                            <button class="btn @if ($post->isFavorite) btn-warning @else btn-secondary @endif btn-sm"
                                    title="@if ($post->isFavorite) Retirer des favoris @else Ajouter aux favoris @endif"
                                    wire:click="switchFavoritePost({{ $post->id }})" type="button">
                                <i class="bx bx-star"></i>
                            </button>
                            <!-- Article deja lu ? : class success si deja lu -->
                            <div class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                                 type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                                <i class="bx bx-message-alt-check"></i>
                            </div>
                          @if ($mode == 'edition')
                           @can('delete', $post)
                            <button wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling : 'deletePostFromRubric', postId : {{ $post->id }}}})" type="button" class="btn btn-sm btn-danger" title="Supprimer">
                                <i class="bx bx-trash"></i>
                            </button>
                           @endcan
                          @endif
                        </div>
                    </div>
                </div>
            </div>
           @endcan
          @endforeach
            @include('includes.pagination', ['elements' => $favoritesPosts, 'perPage' => 'perPage'])
         @endif
        </div>
    </div>
</section>
