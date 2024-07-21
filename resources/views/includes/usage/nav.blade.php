<nav id="navbar" class="navbar">
    <ul>
      @foreach ($rubrics as $rubric)
       @can('access', $rubric)
        <li @if ($rubric->hasChilds()) class="dropdown" role="button" @endif>
          @if ($rubric->hasChilds())
            <a href="#">
                <div class="d-flex flex-wrap justify-content-around">
                    <span class="material-icons">{{ $rubric->icon }}</span>
                    <span class="mx-1">{{ $rubric->name }}</span>
                </div>
                <i class="bi bi-chevron-down"></i>
            </a>
          @else
            <a @if ($currentRoute == $rubric->route())
                href="#{{ $viewBag->template }}" class="nav-link scrollto"
              @else
                href="{{ $rubric->route() }}" class="nav-link"
              @endif>
                <div class="d-flex flex-wrap justify-content-around">
                    <span class="material-icons">{{ $rubric->icon }}</span>
                    <span class="mx-1">{{ $rubric->name }}</span>
                </div>
            </a>
          @endif
          @if ($rubric->hasChilds())
            <ul>
              @foreach ($rubric->childs as $childRubric)
               @can('access', $childRubric)
                <li>
                    <a class="justify-content-start"
                      @if ($currentRoute == $childRubric->route())
                        href="#{{ $viewBag->template }}" class="nav-link scrollto"
                      @else
                        href="{{ $childRubric->route() }}" class="nav-link"
                      @endif>
                        <span class="material-icons fs-5 ms-0 me-1">{{ $childRubric->icon }}</span>
                        {{ $childRubric->name }}
                    </a>
                </li>
               @endcan
              @endforeach
            </ul>
          @endif
        </li>
       @endcan
      @endforeach
        <!-- A laisser en dur à la fin du menu -->
       @can('viewAny', 'App\\App')
        <li>
            <a href="#apps" class="nav-link scrollto" title="Mes applications">
                <div class="myapps">
                    <span class="material-icons me-1">apps</span>Mes applis
                </div>
            </a>
        </li>
       @endcan
        <li>
            <a href="#search" class="nav-link scrollto" title="Rechercher...">
                <span class="bx bx-search-alt fs-1"></span>
            </a>
        </li>
        <li class="dropdown" role="button">
            <a href="#">
                <span class="bx bx-user-circle fs-1"></span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul class="end-0">
                <li>
                    <a href="/infos" class="dropdown-item">Mes infos</a>
                </li>
                <li>
                    <a href="/favoris" class="dropdown-item">Mes favoris</a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item text-danger justify-content-start"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="bx bx-log-in-circle fs-5 me-1"></span>{{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    </ul>
    <i class="bi bi-list mobile-nav-toggle"></i>
</nav>
