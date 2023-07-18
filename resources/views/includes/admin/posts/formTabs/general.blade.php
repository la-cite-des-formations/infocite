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
               <div wire:ignore.self>
                    <button wire:ignore.self id="dropdownMenu2" data-toggle="dropdown" aria-expanded="false" type="button"
                            class="btn btn-light choice-icon-btn dropdown-toggle">
                        <span class="material-icons mr-1 mt-1">{{ (!($post->icon) ? '...' : $post->icon) }}</span>
                    </button>
                <div wire:ignore.self class="dropdown-menu text-muted ml-1 p-1 border-0">
                    <div class="input-group mb-1 border-0">
                        <div class="input-group-text text-secondary border-0">
                            <span class="material-icons">search</span>
                        </div>
                        <input wire:model='searchIcons' type="text" class="form-control search-icons border-0" placeholder="Rechercher...">
                    </div>
                    <div id="post-icon" wire:model="post.icon" type="input" style="max-width: 400px; max-height: 202px;"
                         class="d-flex flex-wrap overflow-auto justify-content-start p-0 border-0">
                      @foreach($icons as $miName => $miCode)
                        <button wire:click="choiceIcon('{{$miName}}', 'post')" type="button" value='{{ $miName }}' title='{{ $miName }}'
                                class='btn btn-sm {{ $post->icon === $miName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                            <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
                        </button>
                      @endforeach
                    </div>
                </div>
            </div>
    </div>
    <div>
      @error('post.content')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
      @enderror
    </div>
    <div class="form-group row">
        <label class="col-1 text-right mt-2" for="post-content">Contenu</label>
        <div wire:ignore class="col-10 p-0">
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

