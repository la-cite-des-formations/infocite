<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des utilisateurs par nombre d'applications personnelles</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-4'>
                <div class='row mb-1'>
                    <label for='personalAppsUsersUserTypeFilter' class='col pe-2 col-form-label text-end'>Utilisateurs :</label>
                    <div class='col px-0'>
                        <select id='personalAppsUsersUserTypeFilter' wire:model='statsCollection.personalAppsUsers.filter.userType'
                                class='form-select' aria-label='Filtrer les utilisateurs'>
                            <option value="all">Tous</option>
                            <option value="staff">Personnel</option>
                            <option value="learners">Apprenants</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='row'>
        <div wire:ignore id='personalAppsUsersTop10Chart' class='col' style="height:360px;"></div>
        <div id='personalAppsUsersTop3' class='col my-auto'>
          @foreach($personalAppsUsersTop3 as $i => $user)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$user->identity}" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $allPersonnalAppsUsers->count() }} utilisateurs se servant de {{ $allPersonnalAppsUsers->sum('apps_nb') }} applications personnelles.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('personalAppsUsers')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#personalAppsUsersTable"
            aria-expanded="{{ $statsCollection['personalAppsUsers']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="personalAppsUsersTable">
        {{ $statsCollection['personalAppsUsers']['buttonLabel'] }}
    </button>
</div>
<div id='personalAppsUsersTable' class="container collapse {{ $statsCollection['personalAppsUsers']['buttonLabel']  == 'Détailler...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">person</span>
                        <div class="ms-1">Utilisateur</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">numbers</span>
                        <div class="ms-1">Nombre d'applications personnelles'</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($personalAppsUsers as $user)
            <tr class='row'>
                <td class='col'>{{ $user->identity }}</td>
                <td class='col'>{{ $user->apps_nb }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($personalAppsUsers->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $personalAppsUsers,
        'perPageOptions' => $statsCollection['personalAppsUsers']['perPageOptions'],
        'perPage' => 'statsCollection.personalAppsUsers.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
