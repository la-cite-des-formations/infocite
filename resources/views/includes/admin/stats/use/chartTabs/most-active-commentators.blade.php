<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des commentateurs les plus actifs</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostActiveCommentatorsSchoolYearFilter' class='col-4 pe-2 col-form-label text-end'>Année :</label>
                            <div class='col px-0'>
                                <select id='mostActiveCommentatorsSchoolYearFilter' wire:model='statsCollection.mostActiveCommentators.filter.schoolYear'
                                        class='form-select'>
                                    <option value="">Choisir une année scolaire...</option>
                                  @foreach ($schoolYears as $year)
                                    <option value="{{ $year }}">{{ $year }}-{{ substr($year + 1, 2) }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label for='mostActiveCommentatorsMonthFilter' class='col-4 pe-2 col-form-label text-end'>Mois :</label>
                            <div class='col px-0'>
                                <select id='mostActiveCommentatorsMonthFilter' wire:model='statsCollection.mostActiveCommentators.filter.month'
                                        class='form-select'
                                        @if(!$statsCollection['mostActiveCommentators']['filter']['schoolYear']) disabled @endif>
                                    <option value="">Choisir un mois...</option>
                                  @foreach($schoolYearMonths as $label => $value)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostActiveCommentatorsCommentatorTypeFilter' class='col-6 pe-2 col-form-label text-end'>Commentateurs :</label>
                            <div class='col px-0'>
                                <select id='mostActiveCommentatorsCommentatorTypeFilter' wire:model='statsCollection.mostActiveCommentators.filter.commentatorType'
                                        class='form-select' aria-label='Filtrer les commentateurs'>
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
        <div wire:ignore id='activeCommentatorsTop10Chart' class='col' style="height:360px;"></div>
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
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $activeCommentators->count() }} commentateurs pour un total de {{ $activeCommentators->sum('comments_count') }} commentaires.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostActiveCommentators')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostActiveCommentatorsTable"
            aria-expanded="{{ $statsCollection['mostActiveCommentators']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostActiveCommentatorsTable">
        {{ $statsCollection['mostActiveCommentators']['buttonLabel'] }}
    </button>
</div>
<div id='mostActiveCommentatorsTable' class="container collapse {{ $statsCollection['mostActiveCommentators']['buttonLabel']  == 'Détailler...' ? '' : 'show'}}">
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
                <td class='col'>{{ $commentator->comments_count }}</td>
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
