@extends('layouts.usage-app')

@section('tabSubtitle', " : L'intranet de la Cité des Formations")

@section('nav')
    @include('includes.usage.nav', [
        'currentRoute' => $viewBag->currentRoute,
        'rubrics' => $viewBag->navRubrics
    ])
@endsection

@section('footer-links')
    @include('includes.usage.footer-links', [
        'rubrics' => $viewBag->footerRubrics
    ])
@endsection

@section('content')
    @include('includes.usage.header', ['fixedTop' => TRUE])
    @include("includes.usage.main", ['viewBag' => $viewBag])
    @include('includes.usage.footer')

    @if ($viewBag->template == 'edit-post' || $viewBag->template == 'edit-app')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @livewire($component)
            </div>
        </div>
    </div>
    @livewire('modal-manager', ['parent' => $component])
    @endif
@endsection

@if ($viewBag->template == 'edit-post')
    @section('addJSFiles')
        <!-- tinyMCE JS Files -->
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
        <script src="{{ asset('js/tiny_editor_SC.js') }}" defer></script>
    @endsection
@endif

@if ($viewBag->template == 'edit-post' || $viewBag->template == 'edit-app')
    @section('addJSFiles')
        <!-- modal Files -->
    <script src="{{ asset('js/modalManager.js') }}"></script>
    @endsection
@endif

@if ($viewBag->template == 'org-chart')
    @section('addJSFiles')
        <!-- Google org-chart JS Files -->
        <script src="{{ asset('js/charts/loader.js') }}" referrerpolicy="origin"></script>
        <script src="{{ asset('js/orgChart.js') }}" defer></script>
    @endsection
@endif
