@extends('layouts.usage-app')

@section('tabSubtitle', " : L'intranet de la Cité des Formations")

@section('content')
    @include('includes.usage.header', ['fixedTop' => TRUE])
    <main id="main">
        <div>
            <section id="breadcrumbs" class="breadcrumbs"></section>
            <div class="container tinymce-editor">Hello, World!</div>
        </div>
    </main>
    @include('includes.usage.footer')
@endsection

@section('addJSFiles')
    <!-- tinyMCE JS Files -->
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script src="{{ asset('js/tinymceConfig.js') }}" defer></script>
@endsection
