@extends('layouts.usage-app')

@section('tabSubtitle', " : L'intranet de la Cité des Formations")

@section('nav')
    @include('includes.usage.nav', [
        'currentRoute' => $viewBag->currentRoute,
        'rubrics' => $viewBag->navRubrics,
    ])
@endsection

@section('footer-links')
    @include('includes.usage.footer-links', [
        'rubrics' => $viewBag->footerRubrics,
    ])
@endsection

@section('content')
    @include('includes.usage.header', ['fixedTop' => true])
    @include('includes.usage.main', ['viewBag' => $viewBag])
    @include('includes.usage.footer')
@endsection

@section('addJSFiles')
    @switch($viewBag->template)
        @case('edit-post')
            <!-- tinyMCE JS Files -->
            <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
            <script src="{{ asset('js/tiny_editor_SC.js') }}?v={{ filemtime(public_path('js/tiny_editor_SC.js')) }}" defer></script>
        @break

        @case('agenda')
            <!-- FullCalendar Files -->
            <script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}" referrerpolicy="origin"></script>
            <script src="{{ asset('vendor/fullcalendar/fr.global.min.js') }}" referrerpolicy="origin"></script>
            <script src="{{ asset('js/fullcalendar-script.js') }}?v={{ filemtime(public_path('js/fullcalendar-script.js')) }}"
                defer></script>
        @break

        @case('org-chart')
            <!-- Google org-chart JS Files -->
            <script src="{{ asset('js/charts/loader.js') }}" referrerpolicy="origin"></script>
            <script src="{{ asset('js/charts.js') }}?v={{ filemtime(public_path('js/charts.js')) }}" defer></script>

            @default
                @can('receiveDesktopNotifs')
                    @livewire('fcm-notifs-sw-client-manager', [$viewBag])
                @endcan

        @endswitch
    @endsection
