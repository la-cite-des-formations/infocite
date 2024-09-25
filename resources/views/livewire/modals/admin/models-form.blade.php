@extends('layouts.modal')

@if ($haveTiny ?? FALSE)
    @section('wire-init')
        wire:init='initTinymce'
    @endsection
@endif

@section('body-p', 'py-0')

@section('modal-size', $modalSize ?? '')

@if($mode === 'edition')
    @section('modal-header-options')
        <a wire:click='refresh' role="button"
           title="Rafraîchir" class="text-secondary me-1" id="refreshButton">
            <span class="material-icons">refresh</span>
        </a>
        <a wire:click="switchMode('view')" role="button"
           title="Visualiser" class="text-secondary me-1" id="switchModeViewButton">
            <span class="material-icons">preview</span>
        </a>
    @endsection

    @section('modal-title', 'Modification')
@else
    @section('modal-title', "Création")
@endif

@section('modal-body')
    <form class="row bg-info pt-2 pb-1">
        @csrf
        <div class="col py-2">
            @include('includes.tabs', ['tabsSystem' => $formTabs])
        </div>
    </form>
    @includeWhen(session()->has('message'), 'includes.alert-message')
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">
            {{ $mode === 'edition' ? 'Fermer' : 'Annuler' }}
        </button>
        <button wire:click="save" type="button" class="btn btn-primary me-1">
            {{ $mode === 'edition' ? 'Modifier' : 'Créer' }}
        </button>
      @if ($mode === 'edition' && $canAdd)
        <button wire:click="switchMode('creation')" title="{{ $addButtonTitle }}"
                type="button" class="d-flex btn btn-sm btn-success">
            <span class="material-icons">add</span>
        </button>
      @endif
    </div>
@endsection



