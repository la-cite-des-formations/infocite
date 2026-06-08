<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des articles les mieux notés</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostRatedPostsReaderTypeFilter' class='col-4 pe-2 col-form-label text-end'>Lecteurs :</label>
                            <div class='col px-0'>
                                <select id='mostRatedPostsReaderTypeFilter' wire:model='statsCollection.mostRatedPosts.filter.readerType'
                                        class='form-select' aria-label='Filtrer les lecteurs'>
                                    <option value="all">Tous</option>
                                    <option value="staff">Personnel</option>
                                    <option value="learners">Apprenants</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label class='col-4 pe-2 col-form-label text-end'>Rubrique :</label>
                            <div class='col-8 px-0'>
                                <div class="dropdown w-100" wire:ignore.self>
                                    <button class="form-select w-100 text-start d-flex justify-content-between align-items-center bg-white text-dark" type="button" id="mostRatedPostsRubricIdFilter" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" wire:ignore.self>
                                        <span class="text-truncate">
                                            @if(empty($statsCollection['mostRatedPosts']['filter']['rubricId']))
                                                Choisir des rubriques...
                                            @else
                                                {{ count($statsCollection['mostRatedPosts']['filter']['rubricId']) }} rubrique(s)
                                            @endif
                                        </span>
                                    </button>
                                    <div class="dropdown-menu w-100 p-2" aria-labelledby="mostRatedPostsRubricIdFilter" style="max-height: 300px; overflow-y: auto;" wire:ignore.self>
                                        @php $hasOutput = false; @endphp
                                        @foreach ($rubricFamilies as $parent)
                                            @php
                                                $childs = $parent->childs;
                                                $hasChilds = $childs->isNotEmpty();
                                            @endphp

                                            @if($parent->contains_posts || $hasChilds)
                                                @if($hasOutput)
                                                    <hr class="my-1">
                                                @endif
                                                @php $hasOutput = true; @endphp
                                            @endif

                                            @if ($hasChilds)
                                                <button type="button" wire:click="toggleParentRubric('mostRatedPosts', {{ $parent->id }})" class="dropdown-item fw-bold btn-link text-start px-2 py-1 border-0 bg-transparent text-decoration-none" onclick="event.stopPropagation()">
                                                    <i class="bi bi-folder-fill me-1"></i> {{ $parent->name }}
                                                </button>

                                                @foreach ($childs as $child)
                                                    <div class="form-check dropdown-item px-4 py-1" onclick="event.stopPropagation()">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $child->id }}" id="rubric_rated_{{ $child->id }}" wire:model="statsCollection.mostRatedPosts.filter.rubricId">
                                                        <label class="form-check-label w-100" for="rubric_rated_{{ $child->id }}">
                                                            {{ $child->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @elseif ($parent->contains_posts)
                                                <div class="form-check dropdown-item px-2 py-1" onclick="event.stopPropagation()">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $parent->id }}" id="rubric_rated_{{ $parent->id }}" wire:model="statsCollection.mostRatedPosts.filter.rubricId">
                                                    <label class="form-check-label fw-bold w-100" for="rubric_rated_{{ $parent->id }}">
                                                        <i class="bi bi-file-earmark-text me-1"></i> {{ $parent->name }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='row'>
        <div wire:ignore id='ratedPostsTop10Chart' class='col' style="height:360px;"></div>
        <div id='topThreeRatedPostsList' class='col my-auto'>
          @foreach($ratedPostsTop3 as $i => $post)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$post->title} ({$post->rubric->name}) — ".number_format($post->ratings_avg, 2)." / 5" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $ratedPosts->count() }} articles notés pour un total de {{ $ratedPosts->sum('ratings_count') }} notes.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostRatedPosts')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostRatedPostsTable"
            aria-expanded="{{ $statsCollection['mostRatedPosts']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostRatedPostsTable">
        {{ $statsCollection['mostRatedPosts']['buttonLabel'] }}
    </button>
</div>
<div id='mostRatedPostsTable' class="container collapse {{ $statsCollection['mostRatedPosts']['buttonLabel'] == 'Détailler...' ? '' : 'show'}}">
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
                        <span class="material-icons m-0">star</span>
                        <div class="ms-1">Note moyenne</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">numbers</span>
                        <div class="ms-1">Nombre de notes</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($mostRatedPosts as $post)
            <tr class='row'>
                <td class='col'>{{ $post->title }}</td>
                <td class='col'>{{ $post->rubric->name }}</td>
                <td class='col'>{{ number_format($post->ratings_avg, 2) }} / 5</td>
                <td class='col'>{{ $post->ratings_count }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostRatedPosts->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostRatedPosts,
        'perPageOptions' => $statsCollection['mostRatedPosts']['perPageOptions'],
        'perPage' => 'statsCollection.mostRatedPosts.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
