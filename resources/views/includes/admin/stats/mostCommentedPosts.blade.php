<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des articles les plus commentés</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='mostCommentedPostsReaderTypeFilter' class='col pe-2 col-form-label text-end'>Lecteurs :</label>
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
                    <label for='mostCommentedPostsRubricIdFilter' class='col pe-2 col-form-label text-end'>Rubrique :</label>
                    <div class='col px-0'>
                        <select id='mostCommentedPostsRubricIdFilter' wire:model='statsCollection.mostCommentedPosts.filter.rubric_id'
                                class='form-select' aria-label='Filtrer les rubriques'>
                            <option label='Choisir une rubrique...'></option>
                              @foreach ($rubrics as $rubric)
                                <option value='{{ $rubric->id }}'>{{ $rubric->identity() }}</option>
                              @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='row'>
        <div wire:ignore id='topTenCommentedPostsChart' class='col' style="height:500px;"></div>
        <div id='topThreeCommentedPostsList' class='col my-auto'>
          @foreach($commentedPostsTop3 as $i => $post)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ ($i + 1)." - ".$post->title." ({$post->rubric})" }}
            </div>
          @endforeach
        </div>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostCommentedPosts')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostCommentedPostsTable"
            aria-expanded="{{ $statsCollection['mostCommentedPosts']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="mostCommentedPostsTable">
        {{ $statsCollection['mostCommentedPosts']['buttonLabel'] }}
    </button>
</div>
<div id='mostCommentedPostsTable' class="container collapse {{ $statsCollection['mostCommentedPosts']['buttonLabel'] == 'Voir plus...' ? '' : 'show'}}">
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
                <td class='col'>{{ $post->rubric }}</td>
                <td class='col'>{{ $post->comments_nb }}</td>
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
