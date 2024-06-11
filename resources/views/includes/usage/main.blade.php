<main id="main">

{{--NotificationPush avec Toastify de Bootstrap--}}
    <div class="container mt-5 position-relative">
        <div id="toast-container" style="position: fixed; bottom: 0; right: 0; z-index: 9999;">
        </div>
    </div>

    @livewire("usage.{$viewBag->template}-manager", ['viewBag' => $viewBag])

  @can('viewAny', 'App\\App')

    @livewire('usage.apps-manager', ['viewBag' => $viewBag])
  @endcan
    <section id="search" class="search-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h4 class='title-icon'><i class="bx bx-search-alt me-1"></i>Rechercher sur le site</h4>
                    <p>Saisissez vos mots clés pour rechercher un contenu sur l'intranet</p>
                    <form action="/search">
                        @csrf
                        <input type="text" name="resultat" value="{{ request()->resultat ?? ''}}">
                        <input type="submit" value="Rechercher" title="Lancer la recherche">
                    </form>
                </div>
            </div>
        </div>

    </section>
    @livewire('modal-manager', ['parent' => "usage.{$viewBag->template}-manager"])
</main>
