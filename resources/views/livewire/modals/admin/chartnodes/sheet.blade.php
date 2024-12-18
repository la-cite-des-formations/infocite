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
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">pages</span>
            </div>
            <div class="my-auto">
                <h5 class="mb-0">{{ $chartnode->name }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <div id="orgchart" class="overflow-auto m-3"></div>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Fermer</button>
    </div>
@endsection
