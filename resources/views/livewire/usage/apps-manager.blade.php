<div>
    <section id="apps" class="pricing">
      @can('add', 'App\\App')
        <div class="container d-flex flex-column">
            <div class="align-self-end">
                <div class="input-group" role="group">
                    <a href="{{ route('personal-apps.create') }}" title="Ajouter une application personnelle"
                       type="button" class="d-flex input-group-text btn btn-sm btn-success">
                        <span class="material-icons">add</span>
                    </a>
                </div>
            </div>
        </div>
      @endcan
        <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="section-title">
                <h2 class="title-icon"><i class="material-icons md-36 me-1">apps</i>Mes applications</h2>
                <p>Toutes les applications auxquelles vous pouvez vous connecter depuis l'intranet de la Cité des Formations</p>
            </div>
            <!--Affichage des App sous forme de list. conditionné par l'affichage des postes-->
          @if(session('displayPosts') === 'list')
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-3">Application</th>
                        <th>Descritpion</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach (auth()->user()->myApps() as $i => $app)
                    <tr wire:click="redirectToApp('{{ $app->url }}')" role="button">
                        <td class="p-0">
                            <h3 class="d-flex p-3 m-0">
                              @if (empty($app->favicon))
                                <span class="material-icons ms-0 me-2">{{ $app->icon }}</span>
                              @else
                                <img src="{{ $app->favicon }}" class="me-2" style="width: 25px; height: 25px;">
                              @endif
                                {{ $app->name }}
                            </h3>
                        </td>
                        <td>
                            <h4 class="m-0">
                                <span>{{ $app->description }}</span>
                            </h4>
                        </td>
                        <td>
                          @can('handle', $app)
                            <div wire:click.prefetch='blockRedirection' class="input-group" role="group" aria-label="Actions">
                                <a href="{{ route('personal-apps.edit', ['app_id' => $app->id]) }}"
                                    role="button" class="btn btn-sm btn-success" title="Modifier">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <button wire:click="showModal('confirm', {handling : 'deleteApp', appId : {{ $app->id }}})"
                                        type="button" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                          @endcan
                        </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          @else
            <!--Affichage des App sous forme de grille. conditionné par l'affichage des postes-->
            <div class="row">
              @foreach (auth()->user()->myApps() as $i => $app)
               @can('view', $app)
                <div class="col-lg-3 mt-4"
                    @if ($firstLoad) data-aos="fade-up" data-aos-delay="{{ ($i % 4 + 1) * 100 }}" @endif
                    wire:click="redirectToApp('{{ $app->url }}')" role="button">
                    <div class="position-relative box featured">
                        <a>
                            <h3 class="d-flex">
                              @if (empty($app->favicon))
                                <span class="material-icons ms-0 me-2">{{ $app->icon }}</span>
                              @else
                                <img src="{{ $app->favicon }}" class="logo me-2" style="width: 25px; height: 25px;">
                              @endif
                                {{ $app->name }}
                            </h3>
                        </a>
                        <h4>
                            <span>{{ $app->description }}</span>
                        </h4>
                      @can('handle', $app)
                        <div wire:click.prefetch='blockRedirection' class="position-absolute bottom-0 end-0 mb-3 me-3">
                            <div class="input-group" role="group" aria-label="Actions">
                                <a href="{{ route('personal-apps.edit', ['app_id' => $app->id]) }}"
                                    role="button" class="btn btn-sm btn-success" title="Modifier">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <button wire:click="showModal('confirm', {handling : 'deleteApp', appId : {{ $app->id }}})"
                                        type="button" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                      @endcan
                    </div>
                </div>
               @endcan
              @endforeach
            </div>
          @endif
        </div>
    </section>
</div>
