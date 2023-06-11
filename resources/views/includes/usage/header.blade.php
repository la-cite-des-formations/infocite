<header id="header" class="@if($fixedTop) fixed-top @endif header-inner-pages">
    <div class="container d-flex align-items-center">
        <a href="/" class="col-1 me-auto">
            <img src="{{ asset('img/logo.jpg') }}" class="img-fluid"
                 title="Info-Cité : L'intranet de la Cité des Formations"
                 alt="Info-Cité : L'intranet de la Cité des Formations - Tours">
        </a>
        <!--<h1 class="logo me-auto"><a href="/">Info-Cité</a></h1>-->
        @yield('nav')
        @yield('end-space')
    </div>
</header>
