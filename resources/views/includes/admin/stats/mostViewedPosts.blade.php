<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des articles les plus lus</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='mostViewedPostsReaderTypeFilter' class='col pe-2 col-form-label text-end'>Lecteurs :</label>
                    <div class='col px-0'>
                        <select id='mostViewedPostsReaderTypeFilter' wire:model='statsCollection.mostViewedPosts.filter.readerType'
                                class='form-select' aria-label='Filtrer les lecteurs'>
                            <option value="all">Tous</option>
                            <option value="staff">Personnel</option>
                            <option value="learners">Apprenants</option>
                        </select>
                    </div>
                </div>
                <div class='row mb-1'>
                    <label for='mostViewedPostsRubricIdFilter' class='col pe-2 col-form-label text-end'>Rubrique :</label>
                    <div class='col px-0'>
                        <select id='mostViewedPostsRubricIdFilter' wire:model='statsCollection.mostViewedPosts.filter.rubric_id'
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
        <div wire:ignore id='topTenViewedPostsChart' class='col' style="height:500px;"></div>
        <div id='viewedPostsTop3' class='col my-auto'>
          @foreach($viewedPostsTop3 as $i => $post)
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
    <button wire:click="toggleButton('mostViewedPosts')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostViewedPostsTable"
            aria-expanded="{{ $statsCollection['mostViewedPosts']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="mostViewedPostsTable">
        {{ $statsCollection['mostViewedPosts']['buttonLabel'] }}
    </button>
</div>
<div id='mostViewedPostsTable' class="container collapse {{ $statsCollection['mostViewedPosts']['buttonLabel']  == 'Voir plus...' ? '' : 'show'}}">
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
                        <div class="ms-1">Nombre de vues</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($mostViewedPosts as $post)
            <tr class='row'>
                <td class='col'>{{ $post->title }}</td>
                <td class='col'>{{ $post->rubric }}</td>
                <td class='col'>{{ $post->views_nb }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostViewedPosts->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostViewedPosts,
        'perPageOptions' => $statsCollection['mostViewedPosts']['perPageOptions'],
        'perPage' => 'statsCollection.mostViewedPosts.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
<hr class='m-5'>
