@extends('layouts.modal')

@section('modal-title', "Liens hiérarchiques - Génération automatique")

@if(!$autoGenerationPerformed && $employees->count())
    @section('modal-body')
        <div class="alert alert-info">
            <dl class="row my-0">
                <dt class="col-12">Génération des liens hiérarchiques pour les acteurs suivants :</dt>
                <dd class="col-12">
                    <ul class="m-0">
                    @foreach($employees as $employee)
                        <li>{{ $employee->identity() }}</li>
                    @endforeach
                    </ul>
                </dd>
            </dl>
        </div>
        @includeWhen(session()->has('message'), 'includes.alert-message')
    @endsection

    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
      @if(!session()->has('message'))
        <button wire:click="build" type="button" class="btn btn-primary mr-1">Générer</button>
      @endif
    @endsection
@elseif($autoGenerationPerformed)
    @section('modal-body')
        @includeWhen(session()->has('message'), 'includes.alert-message')
    @endsection
    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
    @endsection
@else
    @section('modal-body')
        <div class="alert alert-warning">Aucune sélection active. Souhaitez-vous générer tous les liens hiérarchiques correspondant à l'état actuel du filtre ?</div>
    @endsection
    @section('modal-footer')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
        <button wire:click="build('all')" type="button" class="btn btn-primary mr-1">Oui</button>
    @endsection
@endif
