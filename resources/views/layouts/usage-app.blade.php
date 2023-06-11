<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('includes.usage.head')
    </head>
    <body>
        @yield('content')
        @yield('addJSFiles')
    </body>
</html>
