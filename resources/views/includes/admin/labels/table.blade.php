@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">local_offer</span>
            </div>
            Libellé référent
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">person</span>
            </div>
            Acteur
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="btn-group mr-1">
                <span class="material-icons">pages</span>
            </div>
            Processus
        </th>
    </tr>
@endsection

@isset($referents)
 @section('table-body')
  @foreach ($referents as $referent)
    <tr class="row">
        <td scope="row" class="col-4">{{ $referent->label }}</td>
        <td scope="row" class="col-4">{{ $referent->identity }}</td>
        <td scope="row" class="col-4">{{ $referent->process }}</td>
    </tr>
  @endforeach
 @endsection
@endisset
