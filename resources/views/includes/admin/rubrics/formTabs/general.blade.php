<div class="col pt-2">
  @error('rubric.is_parent')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 form-check-label text-end" for="parent-rubric">Rubrique parent</label>
        <div class="form-check col-6 ms-1">
            <input id="parent-rubric" wire:model="rubric.is_parent"
                   type="checkbox" class="form-check-input" @if ($rubric->contains_posts || $rubric->parent_id) disabled @endif>
        </div>
    </div>
  @error('rubric.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-name">Nom</label>
        <div class="col-6">
            <input  id="rubric-name" wire:model="rubric.name" type="input"
                    class="form-control" placeholder="Nom de la rubrique">
        </div>
    </div>
  @error('rubric.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-title">Titre</label>
        <div class="col-6">
            <input  id="rubric-title" wire:model="rubric.title" type="input"
                    class="form-control" placeholder="Titre de la rubrique">
        </div>
    </div>
  @error('rubric.description')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end mb-auto" for="rubric-description">Description</label>
        <div class="col-6">
            <textarea id="rubric-description" wire:model="rubric.description" type="input" rows="3"
                  class="form-control" placeholder="Description de la rubrique"></textarea>
        </div>
    </div>
  @error('rubric.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-icon">Icône</label>
        @include('includes.icon-picker', ['model' => 'rubric', 'inAdminInterface' => TRUE])
    </div>
  @error('rubric.position')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-position">Position</label>
        <div class="col-6">
            <select id="rubric-position" wire:model="rubric.position" type="input" class="form-select">
                <option label="Choisir la position..."></option>
              @foreach(AP::getRubricPositions() as $positionKey => $positionDescription)
                <option value='{{ $positionKey }}'>{{ $positionDescription }}</option>
              @endforeach
            </select>
        </div>
    </div>
 @if (!$rubric->is_parent)
  @error('rubric.parent_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-parent-id">Parent</label>
        <div class="col-6">
            <select id="rubric-parent-id" wire:model="rubric.parent_id" type="input" class="form-select">
                <option label="Choisir le parent..."></option>
              @foreach($rubrics as $rubricOption)
                <option value='{{ $rubricOption->id }}'>{{ $rubricOption->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
 @endif
  @error('rubric.rank')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-rank">Ordre</label>
        <div class="col-6">
            <input id="rubric-rank" wire:model="rubric.rank" type="input"
               class="form-control" placeholder="Ordre de la rubrique">
        </div>
    </div>
 @if (!$rubric->is_parent)
  @error('rubric.contains_posts')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 form-check-label text-end" for="with-posts">Avec articles</label>
        <div class="form-check col-6 ms-1">
            <input id="with-posts" wire:model="rubric.contains_posts"
                   type="checkbox" class="form-check-input" @if ($rubric->posts->isNotEmpty()) disabled @endif>
        </div>
    </div>
 @endif
 @can('adminSegment', $rubric)
  @error('rubric.segment')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-segment">Segment</label>
        <div class="col-6">
            <input  id="rubric-segment" wire:model="rubric.segment" type="input"
                    class="form-control" placeholder="Segment d'accès à la rubrique">

        </div>
    </div>
 @endcan
 @if (!$rubric->is_parent)
  @error('rubric.view')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-3'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-3 col-form-label text-end" for="rubric-view">Vue</label>
        <div class="col-6">
            <input id="rubric-view" wire:model="rubric.view" type="input"
               class="form-control" placeholder="Nom du modèle de vue">
        </div>
    </div>
 @endif
</div>
