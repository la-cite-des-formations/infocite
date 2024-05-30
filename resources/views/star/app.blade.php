<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('star.elt.head')
    </head>
    <body>
        @include('star.elt.header')
        @yield('content')
        @yield('addJSFiles')
    </body>
</html>