<header>

    <!-- logo -->
    <a href="/star" class="logo my-1">
        <img src="{{ asset('img/star_logo_navbar.png') }}" title="Info-Cité : STAR" alt="Logo de l'outils STAR">
    </a>
    
    <!-- NAV -->
    @include('star.elt.nav')
    
    <!-- account section -->
    <div class="account">
        <a>
            <span class="bx bx-user-circle display-6"></span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <ul>
            <li>
                <a href="/infos" class="nav-link">Mes infos</a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="nav-link text-danger justify-content-start" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="bx bx-log-in-circle me-1"></span>{{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

</header>

<style>
    header {
        display: flex;
        background-color: #212529;
        justify-content: space-between;
        align-items: center;
        padding-inline: 10px;
    }
</style>