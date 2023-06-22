<header id="header" class="@if($fixedTop) fixed-top @endif header-inner-pages">
    <div class="d-flex justify-content-between align-items-center mx-5">
        <a href="/" class="logo my-1">
            <img src="{{ asset('img/logo_info_cite.jpg') }}"
                 title="Info-Cité : L'intranet de la Cité des Formations"
                 alt="Info-Cité : L'intranet de la Cité des Formations - Tours">
        </a>
        <!--<h1 class="logo me-auto"><a href="/">Info-Cité</a></h1>-->
        @yield('nav')
        @yield('end-space')
    </div>
</header>
