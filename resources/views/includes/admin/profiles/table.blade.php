@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col @cannot('deleteAny', ['App\\User', TRUE]) p-2 @endcannot">
            <div class="d-flex align-items-center">
              @can('deleteAny', ['App\\User', TRUE])
                <div class="btn-group dropstart">
                    <button type="button" class="d-flex btn btn-sm btn-dark dropdown-toggle px-1" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="Gérer la sélection des profils">
                        <span class="material-icons">portrait</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:onchoiceSelection('profile-cbx', 'all')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box</span>
                            Tous
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('profile-cbx', 'none')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">check_box_outline_blank</span>
                            Aucun
                        </a></li>
                        <li><a href="javascript:onchoiceSelection('profile-cbx', 'reverse')" class="d-flex dropdown-item">
                            <span class="material-icons md-18 ms-0 me-1">swap_horiz</span>
                            Inverser
                        </a></li>
                    </ul>
                </div>
              @else
                <span class="material-icons me-1">portrait</span>
              @endcannot
                Profil
            </div>
        </th>
        <th scope="col" class="col py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">scatter_plot</span>
                <div class="ms-1">Nombre d'utilisateurs associés</div>
            </div>
        </th>
        <th scope="col" class="col-3 d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
               @can('create', ['App\\User', TRUE])
                <button wire:click="showModal('edit', {mode : 'creation'})"
                        class="d-flex btn btn-sm btn-success me-1" title="Ajouter un profil">
                    <span class="material-icons">add</span>
                </button>
              @endcan
              @can('deleteAny', ['App\\User', TRUE])
                <button wire:click="showModal('delete', getSelectionIDs('profile-cbx'))"
                        class="d-flex btn btn-sm btn-danger" title="Supprimer les profils selectionnés">
                    <span class="material-icons">delete</span>
                </button>
              @endcan
            </div>
        </th>
    </tr>
@endsection

@isset($profiles)
 @section('table-body')
  @foreach ($profiles as $profile)
   @if(auth()->user()->canany(['view', 'update', 'delete'], [$profile, TRUE]) || auth()->user()->can('adminRights', ['App\\User', TRUE]))
    <tr class="row">
        <td scope="row" class="col">
          @can('deleteAny', ['App\\User', TRUE])
            <div class="form-check">
                <input type="checkbox" class="form-check-input profile-cbx" id="{{ $profile->id }}">
                <label  class="form-check-label text-primary"
                        for="{{ $profile->id }}" title="{{ $profile->login }}">{{ $profile->first_name }}</label>
            </div>
          @else
                <div class="text-primary">{{ $profile->first_name }}</div>
          @endcan
        </td>
        <td class="col">
            {{ $profile->users->count() }}
        </td>
        <td class="col-3 d-flex justify-content-end mb-auto">
          @if(auth()->user()->can('view', [$profile, TRUE]) || auth()->user()->can('adminRights', ['App\\User', TRUE]))
            <a wire:click="showModal('edit', {mode : 'view', id : {{ $profile->id }}})"
                class="spot spot-info text-info" role="button" title="Visualiser">
                <span class="material-icons">preview</span>
            </a>
          @endcan
          @can('update', [$profile, TRUE])
            <a wire:click="showModal('edit', {mode : 'edition', id : {{ $profile->id }}})"
                class="spot spot-success text-success" role="button" title="Modifier">
                <span class="material-icons">mode</span>
            </a>
          @endcan
          @can('delete', [$profile, TRUE])
            <a wire:click="showModal('delete', [{{ $profile->id }}])"
                class="spot spot-danger text-danger" role="button" title="Supprimer">
                <span class="material-icons">delete</span>
            </a>
          @endcan
        </td>
    </tr>
   @endif
  @endforeach
 @endsection
@endisset
