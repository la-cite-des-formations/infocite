@extends('layouts.modal')

@section('modal-title', "Processus fonctionnels - Génération automatique")
@if($groups->count())
    @section('body-p', 'py-0')
@endif

@section('modal-header-options')
@endsection

@section('modal-body')
  @if($groups->count())
    <div class="row bg-info p-3 mb-3">
        <div class="form-group row flex-fill my-1 mx-0">
            <label class="col-3 text-right my-auto pr-2" for="processes-format">Mise en forme</label>
            <select id="process-format" wire:model="formatId" type="input" class="col form-control">
                <option label="Choisir la mise en forme..."></option>
              @foreach($formats as $format)
                <option value='{{ $format->id }}' class='alert alert-{{ $format->style }}'>{{ $format->name }}</option>
              @endforeach
            </select>
        </div>
      @error('formatId')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
      @enderror
    </div>
  @endif
    <div class="alert alert-info">
        <dl class="row my-0">
            <dt class="col-12">Groupes process à rattacher :</dt>
            <dd class="col-12">
              @if($groups->count())
                <ul class="m-0">
                  @foreach($groups as $group)
                    <li>{{ $group->name }}</li>
                  @endforeach
                </ul>
              @else
                Tous les groupes process sont rattachés à des processus fonctionnels.
              @endif
            </dd>
          @if($processes->count())
            <dt class="col-12">Processus fonctionnels existants :</dt>
            <dd class="col-12">
                <ul class="m-0">
                  @foreach($processes as $process)
                    <li>{{ "{$process->name} => groupe {$process->group->name}" }}</li>
                  @endforeach
                </ul>
            </dd>
          @endif
        </dl>
    </div>
    @includeWhen(session()->has('message'), 'includes.alert-message')
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">{{ $groups->count() ? 'Annuler' : 'Fermer'}}</button>
      @if($groups->count())
        <button wire:click="build" type="button" class="btn btn-primary mr-1">Générer</button>
      @endif
    </div>
@endsection
