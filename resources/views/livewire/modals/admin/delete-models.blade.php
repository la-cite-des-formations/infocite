@extends('layouts.modal')

@section('modal-title', 'Suppression')

@if(!$deletionPerformed && $models->count())
    @section('modal-body')
        <div class="alert alert-info">
            <div class="fw-bold">{{ $headerModelsList }}</div>
            <ul>
              @foreach($models as $model)
                <li>
                  @switch(TRUE)
                   @case(isset($modelInfo['function']) && isset($modelInfo['params']))
                    {{ $model->{$modelInfo['function']}($modelInfo['params']) }}
                   @break
                   @case(isset($modelInfo['function']))
                    {{ $model->{$modelInfo['function']}() }}
                   @break
                   @case(isset($modelInfo['field']))
                    {{ $model->{$modelInfo['field']} }}
                   @break
                  @endswitch
                </li>
              @endforeach
            </ul>
        </div>
        @includeWhen(session()->has('message'), 'includes.alert-message')
    @endsection

    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
      @if(!session()->has('message'))
        <button wire:click="delete" type="button" class="btn btn-primary" id="deleteButton">Supprimer</button>
      @endif
    @endsection
@elseif($deletionPerformed)
    @section('modal-body')
        <div class="alert alert-success">Suppression effectuée avec succès.</div>
    @endsection
@else
    @section('modal-position', 'modal-dialog-centered')
    @section('modal-body')
        <div class="alert alert-warning">Aucune sélection active. Recommencer SVP</div>
    @endsection
@endif

@if($deletionPerformed || !$models->count())
    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
    @endsection
@endif
