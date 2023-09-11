<div>
    <section id="apps" class="pricing">
      @can('add', 'App\\App')
        <div class="container d-flex flex-column">
            <div class="align-self-end">
                <div class="input-group" role="group">
                    <a href="{{ "/personal-apps/create" }}" title="Ajouter une application personnelle"
                       type="button" class="d-flex input-group-text btn btn-sm btn-success">
                        <span class="material-icons">add</span>
                    </a>
                </div>
            </div>
        </div>
      @endcan
        <div class="container" data-aos="fade-up">
            <div class="section-title">
                <h2 class="title-icon"><i class="bx bx-extension me-1"></i>Mes applications</h2>
                <p>Toutes les applications auxquelles vous pouvez vous connecter depuis l'intranet de la Cité des Formations</p>
            </div>
            <div class="row">
              @foreach (auth()->user()->myApps() as $i => $app)
               @can('view', $app)
                <div class="col-lg-3 mt-4" data-aos="fade-up" data-aos-delay="{{ ($i % 4 + 1) * 100 }}">
                    <div class="position-relative box featured">
                        <a href='{{ $app->url }}' target='_blank'>
                            <h3>
                              @if (empty($app->favicon))
                                <span class="material-icons me-1">{{ $app->icon }}</span>
                              @else
                                <img src="{{ $app->favicon }}" class="logo me-1 mb-2" style="width: 25px; height: 25px;">
                              @endif
                                {{ $app->name }}
                            </h3>
                        </a>
                        <h4><span>{{ $app->description }}</span></h4>
                      @can('handle', $app)
                        <div class="position-absolute bottom-0 end-0 mb-3 me-3">
                            <div class="input-group" role="group" aria-label="Actions">
                                <a href="{{ "/personal-apps/{$app->id}/edit" }}" role="button" class="btn btn-sm btn-success" title="Modifier">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <button wire:click="showModal('confirm', {handling : 'deleteApp', appId : {{ $app->id }}})" type="button" class="btn btn-sm btn-danger" title="Supprimer">
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
        </div>
    </section>
</div>
