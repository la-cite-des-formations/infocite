<header id="header" class="d-flex @if($fixedTop) fixed-top @endif header-inner-pages">
    <div class="flex-fill d-flex justify-content-between align-items-center mx-5">
        <a href="/">
            <img src="{{ asset('img/logo_infocite.png') }}"
                    title="Info-Cité : L'intranet de la Cité des Formations"
                    alt="Info-Cité : L'intranet de la Cité des Formations - Tours"
                    height="48px">
        </a>
        <!--<h1 class="logo me-auto"><a href="/">Info-Cité</a></h1>-->
        @yield('nav')
        @yield('end-space')
    </div>
</header>
