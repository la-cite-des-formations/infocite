<nav id="navbar" class="navbar">
    <ul>
      @foreach ($rubrics as $rubric)
       @can('view', $rubric)
        <li @if ($rubric->hasChilds()) class="dropdown" role="button" @endif>
          @if ($rubric->hasChilds())
            <a href="javascript:void(0)">
                <span>{{ $rubric->name }}</span>
                <i class="bi bi-chevron-down"></i>
            </a>
          @else
            <a  @if ($currentRoute == $rubric->route())
                    href="#breadcrumbs" class="nav-link scrollto"
                @else
                    href="{{ $rubric->route() }}" class="nav-link"
                @endif>{{ $rubric->name }}</a>
          @endif
          @if ($rubric->hasChilds())
            <ul>
              @foreach ($rubric->childs as $childRubric)
               @can('view', $childRubric)
                <li>
                    <a  @if ($currentRoute == $childRubric->route())
                            href="#breadcrumbs" class="nav-link scrollto"
                        @else
                            href="{{ $childRubric->route() }}" class="nav-link"
                        @endif>{{ $childRubric->name }}</a>
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
            <a href="#apps" class="nav-link scrollto py-0" title="Mes applications">
                <div class="myapps">
                    <span class="bx bx-extension me-1"></span>Mes applis
                </div>
            </a>
        </li>
       @endcan
        <li>
            <a href="#search" class="nav-link scrollto py-0" title="Rechercher...">
                <span class="bx bx-search-alt display-6"></span>
            </a>
        </li>
        <li class="dropdown text-white me-4" role="button">
            <a class="py-0">
                <span class="bx bx-user-circle display-6"></span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul>
                <li>
                    <a href="/infos" class="nav-link">Mes infos</a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="nav-link text-danger justify-content-start"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       <span class="bx bx-log-in-circle me-1"></span>{{ __('Logout') }}
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
