<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des utilisateurs notant le plus</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostActiveRatersRaterTypeFilter' class='col-6 pe-2 col-form-label text-end'>Utilisateurs :</label>
                            <div class='col px-0'>
                                <select id='mostActiveRatersRaterTypeFilter' wire:model='statsCollection.mostActiveRaters.filter.raterType'
                                        class='form-select' aria-label='Filtrer les notateurs'>
                                    <option value="all">Tous</option>
                                    <option value="staff">Personnel</option>
                                    <option value="learners">Apprenants</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='row'>
        <div wire:ignore id='activeRatersTop10Chart' class='col' style="height:360px;"></div>
        <div id='activeRatersTop3' class='col my-auto'>
          @foreach($activeRatersTop3 as $i => $rater)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$rater->identity} ({$rater->ratings_count} note".($rater->ratings_count > 1 ? 's' : '').")" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $activeRaters->count() }} notateurs pour un total de {{ $activeRaters->sum('ratings_count') }} notes.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostActiveRaters')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostActiveRatersTable"
            aria-expanded="{{ $statsCollection['mostActiveRaters']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostActiveRatersTable">
        {{ $statsCollection['mostActiveRaters']['buttonLabel'] }}
    </button>
</div>
<div id='mostActiveRatersTable' class="container collapse {{ $statsCollection['mostActiveRaters']['buttonLabel'] == 'Détailler...' ? '' : 'show'}}">
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
                        <div class="ms-1">Articles notés</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">star</span>
                        <div class="ms-1">Note moyenne donnée</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($mostActiveRaters as $rater)
            <tr class='row'>
                <td class='col'>{{ $rater->identity }}</td>
                <td class='col'>{{ $rater->ratings_count }}</td>
                <td class='col'>{{ number_format($rater->ratings_avg, 2) }} / 5</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostActiveRaters->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostActiveRaters,
        'perPageOptions' => $statsCollection['mostActiveRaters']['perPageOptions'],
        'perPage' => 'statsCollection.mostActiveRaters.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
