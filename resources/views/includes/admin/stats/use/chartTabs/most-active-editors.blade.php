<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des rédacteurs les plus actifs</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-7'>
                <div class="row">
                    <div class="col-6">
                        <div class='row mb-1'>
                            <label for='mostActiveEditorsSchoolYearFilter' class='col-4 pe-2 col-form-label text-end'>Année :</label>
                            <div class='col px-0'>
                                <select id='mostActiveEditorsSchoolYearFilter' wire:model='statsCollection.mostActiveEditors.filter.schoolYear'
                                        class='form-select'>
                                    <option value="">Choisir une année scolaire...</option>
                                  @foreach ($schoolYears as $year)
                                    <option value="{{ $year }}">{{ $year }}-{{ substr($year + 1, 2) }}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label for='mostActiveEditorsMonthFilter' class='col-4 pe-2 col-form-label text-end'>Mois :</label>
                            <div class='col px-0'>
                                <select id='mostActiveEditorsMonthFilter' wire:model='statsCollection.mostActiveEditors.filter.month'
                                        class='form-select'
                                        @if(!$statsCollection['mostActiveEditors']['filter']['schoolYear']) disabled @endif>
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
                            <label for='mostActiveEditorsEditorTypeFilter' class='col-5 pe-2 col-form-label text-end'>Rédacteurs :</label>
                            <div class='col px-0'>
                                <select id='mostActiveEditorsEditorTypeFilter' wire:model='statsCollection.mostActiveEditors.filter.editorType'
                                        class='form-select' aria-label='Filtrer les rédacteurs'>
                                    <option value="all">Tous</option>
                                    <option value="authors">Auteurs</option>
                                    <option value="correctors">Correcteurs</option>
                                </select>
                            </div>
                        </div>
                        <div class='row mb-1'>
                            <label class='col-4 pe-2 col-form-label text-end'>Rubrique :</label>
                            <div class='col-8 px-0'>
                                <div class="dropdown w-100" wire:ignore.self>
                                    <button class="form-select w-100 text-start d-flex justify-content-between align-items-center bg-white text-dark" type="button" id="mostActiveEditorsRubricIdFilter" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" wire:ignore.self>
                                        <span class="text-truncate">
                                            @if(empty($statsCollection['mostActiveEditors']['filter']['rubricId']))
                                                Choisir des rubriques...
                                            @else
                                                {{ count($statsCollection['mostActiveEditors']['filter']['rubricId']) }} rubrique(s)
                                            @endif
                                        </span>
                                    </button>
                                    <div class="dropdown-menu w-100 p-2" aria-labelledby="mostActiveEditorsRubricIdFilter" style="max-height: 300px; overflow-y: auto;" wire:ignore.self>
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
                                                <button type="button" wire:click="toggleParentRubric('mostActiveEditors', {{ $parent->id }})" class="dropdown-item fw-bold btn-link text-start px-2 py-1 border-0 bg-transparent text-decoration-none" onclick="event.stopPropagation()">
                                                    <i class="bi bi-folder-fill me-1"></i> {{ $parent->name }}
                                                </button>
                                                
                                                @foreach ($childs as $child)
                                                    <div class="form-check dropdown-item px-4 py-1" onclick="event.stopPropagation()">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $child->id }}" id="rubric_editor_{{ $child->id }}" wire:model="statsCollection.mostActiveEditors.filter.rubricId">
                                                        <label class="form-check-label w-100" for="rubric_editor_{{ $child->id }}">
                                                            {{ $child->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @elseif ($parent->contains_posts)
                                                <div class="form-check dropdown-item px-2 py-1" onclick="event.stopPropagation()">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $parent->id }}" id="rubric_editor_{{ $parent->id }}" wire:model="statsCollection.mostActiveEditors.filter.rubricId">
                                                    <label class="form-check-label fw-bold w-100" for="rubric_editor_{{ $parent->id }}">
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
        <div wire:ignore id='activeEditorsTop10Chart' class='col' style="height:360px;"></div>
        <div id='activeEditorsTop3' class='col my-auto'>
          @foreach($activeEditorsTop3 as $i => $editor)
            <div class='d-flex'>
                <div class='rounded-circle mt-1 me-2'
                     style='background-color: {{ $gcColors[$i] }}; height: 12px; min-width: 12px; max-width: 12px;'>&nbsp</div>
                {{ "Top ".($i + 1)." - {$editor->identity}" }}
            </div>
          @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <h5>Soit {{ $activeEditors->count() }} {{ $editorLabel[$statsCollection['mostActiveEditors']['filter']['editorType']] }}
            pour un total de {{ $activeEditors->sum('posts_count') }} articles.</h5>
    </div>
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostActiveEditors')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostActiveEditorsTable"
            aria-expanded="{{ $statsCollection['mostActiveEditors']['buttonLabel'] == 'Détailler...' ? 'false' : 'true'}}" aria-controls="mostActiveEditorsTable">
        {{ $statsCollection['mostActiveEditors']['buttonLabel'] }}
    </button>
</div>
<div id='mostActiveEditorsTable' class="container collapse {{ $statsCollection['mostActiveEditors']['buttonLabel']  == 'Détailler...' ? '' : 'show'}}">
    <table class="table table-sm table-hover admin-table">
        <thead class="table-dark">
            <tr class="row">
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">person</span>
                        <div class="ms-1">Rédacteur</div>
                    </div>
                </th>
                <th scope="col" class="col py-2">
                    <div class="d-flex align-items-center">
                        <span class="material-icons m-0">numbers</span>
                        <div class="ms-1">Nombre d'articles</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
          @foreach ($mostActiveEditors as $editor)
            <tr class='row'>
                <td class='col'>{{ $editor->identity }}</td>
                <td class='col'>{{ $editor->posts_count }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  @if($mostActiveEditors->isNotEmpty())
    @include('includes.pagination', [
        'elements' => $mostActiveEditors,
        'perPageOptions' => $statsCollection['mostActiveEditors']['perPageOptions'],
        'perPage' => 'statsCollection.mostActiveEditors.perPage',
    ])
  @else
    <div class="alert alert-warning">Aucune données statistiques correspondantes.</div>
  @endif
</div>
