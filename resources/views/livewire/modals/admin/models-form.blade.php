@extends('layouts.modal')

@section('body-p', 'py-0')

@section('modal-size', $modalSize ?? '')

@if($mode === 'edition')
    @section('modal-header-options')
        <a wire:click='refresh' role="button"
           title="Rafraîchir" class="text-secondary mr-1" id="refreshButton">
            <span class="material-icons">refresh</span>
        </a>
        <a wire:click="switchMode('view')" role="button"
           title="Visualiser" class="text-secondary mr-1" id="switchModeViewButton">
            <span class="material-icons">preview</span>
        </a>
    @endsection

    @section('modal-title', 'Modification')
@else
    @section('modal-title', "Création")
@endif

@section('modal-body')
    <div class="row bg-info p-3 mb-3">
        @include('includes.tabs', ['tabsSystem' => $formTabs])
    </div>
    @includeWhen(session()->has('message'), 'includes.alert-message')
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">
            {{ $mode === 'edition' ? 'Fermer' : 'Annuler' }}
        </button>
        <button wire:click="save" type="button" class="btn btn-primary mr-1">
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



