@extends('layouts.table')

@section('table-head')
    <tr class="row">
        <th scope="col" class="col-4 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">local_offer</span>
                <div class="ms-1">Libellé référent</div>
            </div>
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">person</span>
                <div class="ms-1">Acteur</div>
            </div>
        </th>
        <th scope="col" class="col-4 py-2">
            <div class="d-flex align-items-center">
                <span class="material-icons m-0">pages</span>
                <div class="ms-1">Processus</div>
            </div>
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
