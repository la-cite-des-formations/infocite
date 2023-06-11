<div class="col">
    <div class="form-row">
        <div class="form-group col mt-3">
            <label title="Sélectionner les articles à retirer"
                   for="rubric-linked-posts">Articles associés</label>
            <select id="rubric-linked-posts" multiple wire:model="selectedLinkedPosts" class="form-control" size="10">
              @foreach($rubric->posts as $post)
                <option value="{{ $post->id }}">
                    {{ $post->title }}
                </option>
              @endforeach
            </select>
        </div>
        <div class="d-flex form-group col-1 my-auto">
            <div class="btn-group-vertical btn-group-sm mx-auto" role="group">
                <button wire:click="add('formTabs')" class="d-flex btn btn-sm btn-success p-0" type="button"
                        title ="Ajouter les articles disponibles sélectionnés aux articles associés">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Retirer les articles associés sélectionnés">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-3">
            <label title="Sélectionner les articles à ajouter"
                   for="rubric-available-posts">Articles disponibles</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Rechercher un article">
                        <span class="material-icons md-18">search</span>
                    </div>
                </div>
                <input wire:model="postSearch" class="form-control" id="rubric-searched-post">
            </div>
            <select id="rubric-available-posts" multiple wire:model="selectedAvailablePosts"
                    class="form-control" size="8">
              @foreach($availablePosts as $post)
                <option value="{{ $post->id }}">
                    {{ $post->title }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
