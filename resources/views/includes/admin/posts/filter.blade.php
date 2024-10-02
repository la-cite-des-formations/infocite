<!-- filtre ... -->
<div>
    <div class="input-group">
        <span class="input-group-text text-secondary material-icons md-18" title="Auteur">face</span>
        <select wire:model='filter.authorId' class="form-select" id="author-filter">
            <option label="Choisir un auteur..."></option>
          @foreach ($authors as $author)
            <option value='{{ $author->id }}'>{{ $author->identity() }}</option>
          @endforeach
        </select>
    </div>
    <div class="input-group mt-3">
        <span class="input-group-text text-secondary material-icons md-18" title="Rubrique">menu</span>
        <select wire:model='filter.rubricId' class="form-select" id="rubric-filter">
            <option label="Choisir une rubrique..."></option>
          @foreach ($rubrics as $rubric)
            <option value='{{ $rubric->id }}'>{{ $rubric->identity() }}</option>
          @endforeach
        </select>
    </div>
    <div class="input-group mt-3">
        <span class="input-group-text text-secondary material-icons md-18" title="État">fact_check</span>
        <select wire:model='filter.phase' class="form-select" id="post-filter">
            <option label="Choisir un état..."></option>
          @foreach (AP::getPostStatus() as $keyStatus => $postStatus)
            <option value='{{ $keyStatus }}'>{{ $postStatus->title }}</option>
          @endforeach
        </select>
    </div>
</div>
