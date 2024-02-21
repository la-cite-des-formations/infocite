<!-- filtre ... -->
<div class="pl-0 col-6">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Auteur">
                <span class="material-icons md-18">face</span>
            </div>
        </div>
        <select wire:model='filter.authorId' class="form-control" id="author-filter">
            <option label="Choisir un auteur..."></option>
          @foreach ($authors as $author)
            <option value='{{ $author->id }}'>{{ $author->identity() }}</option>
          @endforeach
        </select>
    </div>
    <div class="input-group mt-3">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Rubrique">
                <span class="material-icons md-18">menu</span>
            </div>
        </div>
        <select wire:model='filter.rubricId' class="form-control" id="rubric-filter">
            <option label="Choisir une rubrique..."></option>
          @foreach ($rubrics as $rubric)
            <option value='{{ $rubric->id }}'>{{ $rubric->identity() }}</option>
          @endforeach
        </select>
    </div>
    <div class="input-group mt-3">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="État">
                <span class="material-icons md-18">fact_check</span>
            </div>
        </div>
        <select wire:model='filter.phase' class="form-control" id="post-filter">
            <option label="Choisir un état..."></option>
          @foreach (AP::getPostStatus() as $keyStatus => $postStatus)
            <option value='{{ $keyStatus }}'>{{ $postStatus->title }}</option>
          @endforeach
        </select>
    </div>
</div>
