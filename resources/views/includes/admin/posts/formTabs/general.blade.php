@section('wire-init')
    wire:init='initTinymce'
@endsection

  @error('post.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="form-group row">
        <label class="col-1 text-right my-auto" for="post-title">Titre</label>
        <input id="post-title" wire:model="post.title" type="input"
               class="col-10 form-control" placeholder="Titre de l'article">
    </div>
  @error('post.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="form-group row">
        <label class="col-1 text-right my-auto" for="post-icon">Icône</label>
        <input id="post-icon" wire:model="post.icon" type="input"
               class="col-10 form-control" placeholder="Icône de l'article">
    </div>
  @error('post.content')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="form-group row">
        <label class="col-1 text-right mt-2" for="post-content">Contenu</label>
        <div wire:ignore class="col-10">
          <textarea id="post-content" wire:model="post.content" type="input"
                    class="form-control tinymce" placeholder="Contenu de l'article">
          </textarea>
        </div>
    </div>
  @error('post.rubric_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="form-group row">
        <label class="col-1 text-right my-auto" for="post-rubric-id">Rubrique</label>
        <select id="post-rubric-id" wire:model="post.rubric_id" type="input" class="col-10 form-control">
            <option label="Choisir la rubrique..."></option>
          @foreach($rubrics as $rubric)
            <option value='{{ $rubric->id }}'>{{ (is_object($rubric->parent) ? $rubric->parent->name.' / ' : '').$rubric->name }}</option>
          @endforeach
        </select>
    </div>
  @error('post.published')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="form-group row">
        <div class="col-1"></div>
        <div class="form-check col">
            <input id="post-published" wire:model="post.published"
                    type="checkbox" class="form-check-input">
            <label class="my-auto" for="post-published">Publier</label>
        </div>
    </div>

