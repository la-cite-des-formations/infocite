<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Nombre de connexions par jour</h5>
                <p>(semaine dernière + semaine actuelle)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='connectionsUserTypeFilter' class='col pe-2 col-form-label text-end'>Usagers :</label>
                    <div class='col px-0'>
                        <select id='connectionsUserTypeFilter' wire:model='statsCollection.connections.filter.userType'
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
    <div wire:ignore id='connectionsChart'></div>
</div>
<div class='mt-4 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('connections')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#connectionsTable"
            aria-expanded="{{ $statsCollection['connections']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="connectionsTable">
        {{ $statsCollection['connections']['buttonLabel'] }}
    </button>
</div>
<div id='connectionsTable' class="container collapse {{ $statsCollection['connections']['buttonLabel'] == 'Voir plus...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">calendar_month</span>
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
          @foreach ($connections as $connection)
            <tr class='row'>
                <td class='col'>{{ $connection->connected_at->format('d/m/Y') }}</td>
                <td class='col'>{{ $connection->connections_nb }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($connections->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $connections,
        'perPageOptions' => $statsCollection['connections']['perPageOptions'],
        'perPage' => 'statsCollection.connections.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
