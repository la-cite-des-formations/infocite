<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('includes.admin.head')
        @livewireStyles(['nonce' => csp_nonce()])
    </head>
    <body class="bg-dark">
        <div id="app">
          @auth
            @include('includes.admin.nav')
          @endauth
            <main class="py-4">
                @yield('content')
            </main>
        </div>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
        @yield('addJs')
        @livewireScripts(['nonce' => csp_nonce()])
    </body>
</html>
