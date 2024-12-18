@extends('layouts.admin-app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @livewire($component)
            </div>
        </div>
    </div>
    @livewire('modal-manager', ['parent' => $component])
@endsection

@section('addJs')
    <script src="{{ asset('js/selectionManager.js') }}"></script>
    <script src="{{ asset('js/modalManager.js') }}"></script>
    @if ($component == 'admin.posts-manager')
        <!-- tinyMCE JS Files -->
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
        <script src="{{ asset('js/tiny_editor_SC.js') }}" defer></script>
    @endif
    @if (
        $component == 'admin.formats-manager' ||
        $component == 'admin.chartnodes-manager' ||
        $component == 'admin.connections-manager' ||
        $component == 'admin.using-manager' ||
        $component == 'admin.viewing-manager'
    )
        <!-- Google org-chart JS Files -->
        <script src="{{ asset('js/charts/loader.js') }}" referrerpolicy="origin"></script>
        <script src="{{ asset('js/charts.js') }}" defer></script>
    @endif
@endsection
