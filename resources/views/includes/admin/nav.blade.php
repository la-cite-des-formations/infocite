<header id="header">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand logo ms-4" href="{{ url('/dashboard') }}" title="Info-Cité - Tableau de bord">
                <img src="{{ asset('img/favicon.png') }}" alt="Info-Cité">
                <span class="ms-0 align-middle">Tableau de bord</span>
            </a>
            <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-4" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                {{-- <ul class="navbar-nav me-auto">

                </ul> --}}

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->first_name }} {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="material-icons md-24 align-middle">power_settings_new</span>
                                <span class="ms-1 align-middle">{{ __('Logout') }}</span>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                         @foreach (AP::getAllDashboardsFunctions() as $function)
                          @can(is_array($function->gate) ? $function->gate['name'] : $function->gate,
                                is_array($function->gate) ? $function->gate['dashboard'] : NULL)
                            <a
                              @if(isset($function->route['parameters']))
                                 href="{{ route($function->route['name'] ?: 'dashboard', $function->route['parameters']) }}"
                              @else
                                 href="{{ route($function->route['name'] ?: 'dashboard') }}"
                              @endif
                                 class="admin-menu-item dropdown-item card-dashboard-{{ $function->color }}">
                                <span class="@if (empty($function->atRoot)) ms-3 @endif material-icons-outlined md-24 align-middle db-text-{{ $function->color }}">{{ $function->icon_name }}</span>
                                <span class="ms-1 align-middle">{{ $function->title }}</span>
                            </a>
                          @endcan
                         @endforeach
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
