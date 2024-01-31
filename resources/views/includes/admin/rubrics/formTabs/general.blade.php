<div class="col">
  @error('rubric.is_parent')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <div class="col-3"></div>
        <div class="form-check col-7">
            <input id="rubric-is-parent" wire:model="rubric.is_parent"
                   type="checkbox" class="form-check-input" @if ($rubric->contains_posts || $rubric->parent_id) disabled @endif>
            <label class="my-auto" for="rubric-is-parent">Rubrique parent</label>
        </div>
    </div>
  @error('rubric.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-name">Nom</label>
        <input id="rubric-name" wire:model="rubric.name" type="input"
               class="col-7 form-control" placeholder="Nom de la rubrique">
    </div>
  @error('rubric.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-title">Titre</label>
        <input id="rubric-title" wire:model="rubric.title" type="input"
               class="col-7 form-control" placeholder="Titre de la rubrique">
    </div>
  @error('rubric.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-description">Description</label>
        <textarea id="rubric-description" wire:model="rubric.description" type="input" rows="3"
                  class="col-7 form-control" placeholder="Description de la rubrique">
        </textarea>
    </div>
  @error('rubric.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-icon">Icône</label>
               <div wire:ignore.self>
                <button wire:ignore.self id="dropdownMenu2" data-toggle="dropdown" aria-expanded="false" type="button"
                        class="btn btn-light choice-icon-btn dropdown-toggle">
                    <span class="material-icons mr-1 mt-1">{{ (!($rubric->icon) ? '...' : $rubric->icon) }}</span>
                </button>
            <div wire:ignore.self class="dropdown-menu text-muted ml-1 p-1 border-0">
                <div class="input-group mb-1 border-0">
                    <div class="input-group-text text-secondary border-0">
                        <span class="material-icons">search</span>
                    </div>
                    <input wire:model='searchIcons' type="text" class="form-control search-icons border-0" placeholder="Rechercher...">
                </div>
                <div id="rubric-icon" wire:model="rubric.icon" type="input" style="max-width: 400px; max-height: 202px;"
                     class="d-flex flex-wrap overflow-auto justify-content-start p-0 border-0">
                  @foreach($icons as $miName => $miCode)
                    <button wire:click="choiceIcon('{{$miName}}', 'rubric')" type="button" value='{{ $miName }}' title='{{ $miName }}'
                            class='btn btn-sm {{ $rubric->icon === $miName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                        <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
                    </button>
                  @endforeach
                </div>
            </div>
        </div>
    </div>
  @error('rubric.position')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-position">Position</label>
        <select id="rubric-position" wire:model="rubric.position" type="input" class="col-7 form-control">
            <option label="Choisir la position..."></option>
          @foreach(AP::getRubricPositions() as $positionKey => $positionDescription)
            <option value='{{ $positionKey }}'>{{ $positionDescription }}</option>
          @endforeach
        </select>
    </div>
 @if (!$rubric->is_parent)
  @error('rubric.parent_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-parent-id">Parent</label>
        <select id="rubric-parent-id" wire:model="rubric.parent_id" type="input" class="col-7 form-control">
            <option label="Choisir le parent..."></option>
          @foreach($rubrics as $rubricOption)
            <option value='{{ $rubricOption->id }}'>{{ $rubricOption->name }}</option>
          @endforeach
        </select>
    </div>
 @endif
  @error('rubric.rank')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-rank">Ordre</label>
        <input id="rubric-rank" wire:model="rubric.rank" type="input"
               class="col-7 form-control" placeholder="Ordre de la rubrique">
    </div>
 @if (!$rubric->is_parent)
  @error('rubric.contains_posts')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <div class="col-3"></div>
        <div class="form-check col-7">
            <input id="rubric-contains-posts" wire:model="rubric.contains_posts"
                   type="checkbox" class="form-check-input" @if ($rubric->posts->isNotEmpty()) disabled @endif>
            <label class="my-auto" for="rubric-contains-posts">Avec articles</label>
        </div>
    </div>
 @endif
 @can('adminSegment', $rubric)
  @error('rubric.segment')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-segment">Segment</label>
        <input id="rubric-segment" wire:model="rubric.segment" type="input"
               class="col-7 form-control" placeholder="Segment d'accès à la rubrique">
    </div>
 @endcan
 @if (!$rubric->is_parent)
  @error('rubric.view')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="form-group row">
        <label class="col-3 text-right my-auto" for="rubric-view">Vue</label>
        <input id="rubric-view" wire:model="rubric.view" type="input"
               class="col-7 form-control" placeholder="Nom du modèle de vue">
    </div>
 @endif
</div>
