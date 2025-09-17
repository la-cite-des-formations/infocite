<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Nombre de connexions distinctes par jour</h5>
                <p>(semaine dernière + semaine actuelle)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='connectionsByDayUserTypeFilter' class='col pe-2 col-form-label text-end'>Usagers :</label>
                    <div class='col px-0'>
                        <select id='connectionsByDayUserTypeFilter' wire:model='statsCollection.connectionsByDay.filter.userType'
                                class='form-select' aria-label='Filtrer les usagers'>
                            <option value="all">Tous</option>
                            <option value="staff">Personnel</option>
                            <option value="learners">Apprenants</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div wire:ignore id='connectionsByDayChart'></div>
</div>
<div class='mt-4 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('connectionsByDay')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#connectionsByDayTable"
            aria-expanded="{{ $statsCollection['connectionsByDay']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="connectionsByDayTable">
        {{ $statsCollection['connectionsByDay']['buttonLabel'] }}
    </button>
</div>
<div id='connectionsByDayTable' class="container collapse {{ $statsCollection['connectionsByDay']['buttonLabel'] == 'Voir plus...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">today</span>
                        <div class="ms-1">Date</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">numbers</span>
                        <div class="ms-1">Nombre de connexions</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($connectionsByDay as $connection)
            <tr class='row'>
                <td class='col'>{{ $connection->interaction_at->format('d/m/Y') }}</td>
                <td class='col'>{{ $connection->connections_nb }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($connectionsByDay->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $connectionsByDay,
        'perPageOptions' => $statsCollection['connectionsByDay']['perPageOptions'],
        'perPage' => 'statsCollection.connectionsByDay.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
