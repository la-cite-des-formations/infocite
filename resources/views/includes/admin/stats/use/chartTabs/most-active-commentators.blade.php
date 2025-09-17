<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des commentateurs les plus actifs</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='mostActiveCommentatorsCommentatorTypeFilter' class='col pe-2 col-form-label text-end'>Commentateurs :</label>
                    <div class='col px-0'>
                        <select id='mostActiveCommentatorsCommentatorTypeFilter' wire:model='statsCollection.mostActiveCommentators.filter.commentatorType'
                                class='form-select' aria-label='Filtrer les commentateurs'>
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
        <div wire:ignore id='activeCommentatorsTop10Chart' class='col' style="height:500px;"></div>
        <div id='activeCommentatorsTop3' class='col my-auto'>
          @foreach($activeCommentatorsTop3 as $i => $commentator)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$commentator->identity}" }}
            </div>
          @endforeach
        </div>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostActiveCommentators')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostActiveCommentatorsTable"
            aria-expanded="{{ $statsCollection['mostActiveCommentators']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="mostActiveCommentatorsTable">
        {{ $statsCollection['mostActiveCommentators']['buttonLabel'] }}
    </button>
</div>
<div id='mostActiveCommentatorsTable' class="container collapse {{ $statsCollection['mostActiveCommentators']['buttonLabel']  == 'Voir plus...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">person</span>
                        <div class="ms-1">Commentateur</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">numbers</span>
                        <div class="ms-1">Nombre de commentaires</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($mostActiveCommentators as $commentator)
            <tr class='row'>
                <td class='col'>{{ $commentator->identity }}</td>
                <td class='col'>{{ $commentator->comments_nb }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostActiveCommentators->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostActiveCommentators,
        'perPageOptions' => $statsCollection['mostActiveCommentators']['perPageOptions'],
        'perPage' => 'statsCollection.mostActiveCommentators.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
