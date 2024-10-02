@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4">
            <div class="d-flex align-items-center">
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des mises en formes">
                        <span class="material-icons">format_shapes</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('format-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('format-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('format-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
                Mise en forme
            </div>
        </th>
        <th scope="col" class="col-5 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">css</span>
                <div class="ms-1">Style</div>
            </div>
        </th>
        <th scope="col" class="col d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter une mise en forme">
                    <span class="material-icons">add</span>
                </button>
                <button wire:click="showModal('delete', getSelectionIDs('format-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les mises en forme selectionnées">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </th>
    </tr>
@endsection

@isset($formats)
 @section('table-body')
  @foreach ($formats as $format)
    <tr class="row">
        <td scope="row" class="col-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input format-cbx" id="{{ $format->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $format->id }}">{{ $format->name }}</label>
            </div>
        </td>
        <td class="col-5">
            <div class="google-visualization-orgchart-node text-center p-1" style="{{ $format->style }}">
                <div class='fw-bold {{ $format->title_color }}'>Titre</div>
                <div class='{{ "{$format->subtitle_font_style} {$format->subtitle_color}" }}'>sous-titre</div>
            </div>
        </td>
        <td class="col d-flex justify-content-end align-items-center">
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $format->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $format->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
            <a wire:click="showModal('delete', [{{ $format->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
        </td>
    </tr>
  @endforeach
 @endsection
@endisset
