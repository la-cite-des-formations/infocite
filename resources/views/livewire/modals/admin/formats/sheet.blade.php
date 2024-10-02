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
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">format_shapes</span>
            <div class="google-visualization-orgchart-node text-center ms-1 p-1 my-auto flex-fill" style="{{ $format->style }}">
                <div class='fw-bold {{ $format->title_color }}'>{{ $format->name }}</div>
                <div class='{{ "{$format->subtitle_font_style} {$format->subtitle_color}" }}'>contenu</div>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row m-0">
          @if ($format->chartnodes->IsNotEmpty())
            <dt class="col-12 ps-0">Nœuds graphiques correspondant</dt>
            <ul class="ms-2">
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
        <button type="button" class="btn btn-secondary mr-1" data-bs-dismiss="modal">Fermer</button>
    </div>
@endsection
