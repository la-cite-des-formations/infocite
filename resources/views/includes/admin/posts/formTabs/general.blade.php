<div class="col pt-2">
  @error('post.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-title">Titre</label>
        <div class="col-7">
            <input id="post-title" wire:model="post.title" type="input"
               class="form-control" placeholder="Titre de l'article">
        </div>
    </div>
  @error('post.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-icon">Icône</label>
        @include('includes.icon-picker', ['model' => 'post', 'inAdminInterface' => TRUE])
    </div>
    <div>
      @error('post.content')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
      @enderror
    </div>
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end mb-auto" for="post-content">Contenu</label>
        <div wire:ignore class="col-10">
            <textarea id="post-content" wire:model="post.content" type="input"
                      class="form-control tinymce" placeholder="Contenu de l'article">
            </textarea>
        </div>
    </div>
  @error('post.rubric_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-rubric-id">Rubrique</label>
        <div class="col-5">
            <select id="post-rubric-id" wire:model="post.rubric_id" type="input" class="form-select">
                <option label="Choisir la rubrique..."></option>
              @foreach($rubrics as $rubric)
                <option value='{{ $rubric->id }}'>{{ (is_object($rubric->parent) ? $rubric->parent->name.' / ' : '').$rubric->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('post.published')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-published">Publier</div>
        <div class="form-check col ms-1">
            <input  id="post-published" wire:model="post.published"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
</div>
