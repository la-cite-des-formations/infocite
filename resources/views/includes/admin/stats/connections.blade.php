<div class='container'>
    <div wire:ignore id='connectionsChart'></div>
</div>
<div class='my-4 d-flex justify-content-center'>
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
