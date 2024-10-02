@extends('layouts.modal')

@section('wire-init')
    wire:init='drawChartnode'
@endsection

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">pages</span>
            <div class="ms-1 my-auto">
                <h5 class="my-auto">{{ $chartnode->name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <div id="orgchart" class="overflow-auto m-3"></div>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
    </div>
@endsection
