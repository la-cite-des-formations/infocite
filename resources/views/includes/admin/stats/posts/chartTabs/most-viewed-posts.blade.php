<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des articles les plus lus</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostViewedPostsSchoolYearFilter' class='col-4 pe-2 col-form-label text-end'>Année :</label>
                            <div class='col px-0'>
                                <select id='mostViewedPostsSchoolYearFilter' wire:model='statsCollection.mostViewedPosts.filter.schoolYear'
                                        class='form-select'>
                                    <option value="">Choisir une année scolaire...</option>
                                  @foreach ($schoolYears as $year)
                                    <option value="{{ $year }}">{{ $year }}-{{ Str::substr($year + 1, 2) }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label for='mostViewedPostsMonthFilter' class='col-4 pe-2 col-form-label text-end'>Mois :</label>
                            <div class='col px-0'>
                                <select id='mostViewedPostsMonthFilter' wire:model='statsCollection.mostViewedPosts.filter.month'
                                        class='form-select'
                                        @if(!$statsCollection['mostViewedPosts']['filter']['schoolYear']) disabled @endif>
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
                            <label for='mostViewedPostsReaderTypeFilter' class='col-4 pe-2 col-form-label text-end'>Lecteurs :</label>
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
                            <label class='col-4 pe-2 col-form-label text-end'>Rubrique :</label>
                            <div class='col-8 px-0'>
                                <div class="dropdown w-100" wire:ignore.self>
                                    <button class="form-select w-100 text-start d-flex justify-content-between align-items-center bg-white text-dark" type="button" id="mostViewedPostsRubricIdFilter" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" wire:ignore.self>
                                        <span class="text-truncate">
                                            @if(empty($statsCollection['mostViewedPosts']['filter']['rubricId']))
                                                Choisir des rubriques...
                                            @else
                                                {{ count($statsCollection['mostViewedPosts']['filter']['rubricId']) }} rubrique(s)
                                            @endif
                                        </span>
                                    </button>
                                    <div class="dropdown-menu w-100 p-2" aria-labelledby="mostViewedPostsRubricIdFilter" style="max-height: 300px; overflow-y: auto;" wire:ignore.self>
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
                                                <button type="button" wire:click="toggleParentRubric('mostViewedPosts', {{ $parent->id }})" class="dropdown-item fw-bold btn-link text-start px-2 py-1 border-0 bg-transparent text-decoration-none" onclick="event.stopPropagation()">
                                                    <i class="bi bi-folder-fill me-1"></i> {{ $parent->name }}
                                                </button>
                                                
                                                @foreach ($childs as $child)
                                                    <div class="form-check dropdown-item px-4 py-1" onclick="event.stopPropagation()">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $child->id }}" id="rubric_viewed_{{ $child->id }}" wire:model="statsCollection.mostViewedPosts.filter.rubricId">
                                                        <label class="form-check-label w-100" for="rubric_viewed_{{ $child->id }}">
                                                            {{ $child->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @elseif ($parent->contains_posts)
                                                <div class="form-check dropdown-item px-2 py-1" onclick="event.stopPropagation()">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $parent->id }}" id="rubric_viewed_{{ $parent->id }}" wire:model="statsCollection.mostViewedPosts.filter.rubricId">
                                                    <label class="form-check-label fw-bold w-100" for="rubric_viewed_{{ $parent->id }}">
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
        <div wire:ignore id='viewedPostsTop10Chart' class='col' style="height:360px;"></div>
        <div id='viewedPostsTop3' class='col my-auto'>
          @foreach($viewedPostsTop3 as $i => $post)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$post->title} ({$post->rubric->name})" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $viewedPosts->count() }} articles lus pour un total de {{ $viewedPosts->sum('views_count') }} vues.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostViewedPosts')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostViewedPostsTable"
            aria-expanded="{{ $statsCollection['mostViewedPosts']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostViewedPostsTable">
        {{ $statsCollection['mostViewedPosts']['buttonLabel'] }}
    </button>
</div>
<div id='mostViewedPostsTable' class="container collapse {{ $statsCollection['mostViewedPosts']['buttonLabel']  == 'Détailler...' ? '' : 'show'}}">
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
                <td class='col'>{{ $post->rubric->name }}</td>
                <td class='col'>{{ $post->views_count }}</td>
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
