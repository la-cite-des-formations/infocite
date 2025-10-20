<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des articles les plus commentés</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostCommentedPostsSchoolYearFilter' class='col-4 pe-2 col-form-label text-end'>Année :</label>
                            <div class='col px-0'>
                                <select id='mostCommentedPostsSchoolYearFilter' wire:model='statsCollection.mostCommentedPosts.filter.schoolYear'
                                        class='form-select'>
                                    <option value="">Choisir une année scolaire...</option>
                                  @foreach ($schoolYears as $year)
                                    <option value="{{ $year }}">{{ $year }}-{{ substr($year + 1, 2) }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label for='mostCommentedPostsMonthFilter' class='col-4 pe-2 col-form-label text-end'>Mois :</label>
                            <div class='col px-0'>
                                <select id='mostCommentedPostsMonthFilter' wire:model='statsCollection.mostCommentedPosts.filter.month'
                                        class='form-select'
                                        @if(!$statsCollection['mostCommentedPosts']['filter']['schoolYear']) disabled @endif>
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
                            <label for='mostCommentedPostsReaderTypeFilter' class='col-4 pe-2 col-form-label text-end'>Lecteurs :</label>
                            <div class='col px-0'>
                                <select id='mostCommentedPostsReaderTypeFilter' wire:model='statsCollection.mostCommentedPosts.filter.readerType'
                                        class='form-select' aria-label='Filtrer les lecteurs'>
                                    <option value="all">Tous</option>
                                    <option value="staff">Personnel</option>
                                    <option value="learners">Apprenants</option>
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label for='mostCommentedPostsRubricIdFilter' class='col-4 pe-2 col-form-label text-end'>Rubrique :</label>
                            <div class='col px-0'>
                                <select id='mostCommentedPostsRubricIdFilter' wire:model='statsCollection.mostCommentedPosts.filter.rubricId'
                                        class='form-select' aria-label='Filtrer les rubriques'>
                                    <option label='Choisir une rubrique...'></option>
                                  @foreach ($rubrics as $rubric)
                                    <option value='{{ $rubric->id }}'>{{ $rubric->identity() }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='row'>
        <div wire:ignore id='commentedPostsTop10Chart' class='col' style="height:360px;"></div>
        <div id='topThreeCommentedPostsList' class='col my-auto'>
          @foreach($commentedPostsTop3 as $i => $post)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$post->title} ({$post->rubric->name})" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $commentedPosts->count() }} articles commentés pour un total de {{ $commentedPosts->sum('comments_count') }} commentaires.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostCommentedPosts')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostCommentedPostsTable"
            aria-expanded="{{ $statsCollection['mostCommentedPosts']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostCommentedPostsTable">
        {{ $statsCollection['mostCommentedPosts']['buttonLabel'] }}
    </button>
</div>
<div id='mostCommentedPostsTable' class="container collapse {{ $statsCollection['mostCommentedPosts']['buttonLabel'] == 'Détailler...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">article</span>
                        <div class="ms-1">Article</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">menu</span>
                        <div class="ms-1">Rubrique</div>
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
          @foreach ($mostCommentedPosts as $post)
            <tr class='row'>
                <td class='col'>{{ $post->title }}</td>
                <td class='col'>{{ $post->rubric->name }}</td>
                <td class='col'>{{ $post->comments_count }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostCommentedPosts->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostCommentedPosts,
        'perPageOptions' => $statsCollection['mostCommentedPosts']['perPageOptions'],
        'perPage' => 'statsCollection.mostCommentedPosts.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
