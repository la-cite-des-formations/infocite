<div class='container'>
    <div class='container'>
        <div class='row'>
            <div class='col'>
                <h5 class='fw-bold'>Top 10 des rédacteurs les plus actifs</h5>
                <p>(cliquer sur un secteur pour afficher le détail)</p>
            </div>
            <form class='col-5'>
                <div class='row mb-1'>
                    <label for='mostActiveEditorsEditorTypeFilter' class='col pe-2 col-form-label text-end'>Rédacteurs :</label>
                    <div class='col px-0'>
                        <select id='mostActiveEditorsEditorTypeFilter' wire:model='statsCollection.mostActiveEditors.filter.editorType'
                                class='form-select' aria-label='Filtrer les rédacteurs'>
                            <option value="all">Auteurs / Correcteurs</option>
                            <option value="authors">Auteurs</option>
                            <option value="correctors">Correcteurs</option>
                        </select>
                    </div>
                </div>
                <div class='row mb-1'>
                    <label for='mostActiveEditorsRubricIdFilter' class='col pe-2 col-form-label text-end'>Rubrique :</label>
                    <div class='col px-0'>
                        <select id='mostActiveEditorsRubricIdFilter' wire:model='statsCollection.mostActiveEditors.filter.rubric_id'
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
        <div wire:ignore id='activeEditorsTop10Chart' class='col' style="height:500px;"></div>
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
</div>
<div class='mt-1 mb-3 d-flex justify-content-center'>
    <button wire:click="toggleButton('mostActiveEditors')" class='btn btn-success' type="button"
            data-bs-toggle="collapse" data-bs-target="#mostActiveEditorsTable"
            aria-expanded="{{ $statsCollection['mostActiveEditors']['buttonLabel'] == 'Voir plus...' ? 'false' : 'true'}}" aria-controls="mostActiveEditorsTable">
        {{ $statsCollection['mostActiveEditors']['buttonLabel'] }}
    </button>
</div>
<div id='mostActiveEditorsTable' class="container collapse {{ $statsCollection['mostActiveEditors']['buttonLabel']  == 'Voir plus...' ? '' : 'show'}}">
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
                <td class='col'>{{ $editor->posts_nb }}</td>
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
<hr class='m-5'>
