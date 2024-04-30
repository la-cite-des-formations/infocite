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
                <span class="material-icons-outlined md-36">format_shapes</span>
            </div>
            <div class="google-visualization-orgchart-node text-center p-1 my-auto flex-fill" style="{{ $format->style }}">
                <div class='font-weight-bold {{ $format->title_color }}'>{{ $format->name }}</div>
                <div class='{{ "{$format->subtitle_font_style} {$format->subtitle_color}" }}'>contenu</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
          @if ($format->chartnodes->IsNotEmpty())
            <dt class="col-12 pl-0 mt-2">Nœuds graphiques correspondant</dt>
            <ul>
              @foreach ($format->chartnodes as $chartnode)
                <li>{{ $chartnode->name }}</li>
              @endforeach
            </ul>
          @endif
        </dl>
    </div>
@endsection

@section('modal-footer')
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Fermer</button>
    </div>
@endsection
