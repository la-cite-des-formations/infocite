<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height: 100%">
    <head>
        @include('includes.admin.head')
        @livewireStyles
    </head>
    <body class="h-100 bg-dark">
        <div id="app" class="h-100">
          @auth
            @include('includes.admin.nav')
          @endauth
            <main class="py-4 h-100">
                @yield('content')
            </main>
        </div>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        @yield('addJs')
        @livewireScripts
    </body>
</html>
