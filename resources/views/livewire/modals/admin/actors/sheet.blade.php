@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">admin_panel_settings</span>
            </div>
            <div class="my-auto">
                <h5 class="mb-0"></h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Fermer</button>
    </div>
@endsection
