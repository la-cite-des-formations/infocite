<div class="col">
    <div class="form-row">
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les articles à retirer"
                   for="rubric-linked-posts" class="m-auto py-2">Articles de la rubrique</label>
            <select id="rubric-linked-posts" multiple wire:model="selectedLinkedPosts"
                    class="form-control flex-fill" size="10">
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
                        title ="Transférer les articles sélectionnés de l'autre rubrique vers la rubrique gérée">
                    <span class="material-icons md-18">arrow_left</span>
                </button>
                <button wire:click="remove('formTabs')" class="d-flex btn btn-sm btn-danger p-0" type="button"
                        title ="Transférer les articles sélectionnés de la rubrique gérée vers l'autre rubrique">
                    <span class="material-icons md-18">arrow_right</span>
                </button>
            </div>
        </div>
        <div class="form-group col mt-1 pt-1">
            <label title="Sélectionner les articles à ajouter"
                   for="rubric-available-posts" class="m-auto py-2">Articles d'une autre rubrique</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text text-secondary" title="Choisir une rubrique">
                        <span class="material-icons md-18">menu</span>
                    </div>
                </div>
                <select wire:model="targetRubricId" class="form-control" id="target-rubric-id">
                  @foreach($availableTargetRubrics as $availableTargetRubric)
                    <option value="{{ $availableTargetRubric->id }}">{{ $availableTargetRubric->identity() }}</option>
                  @endforeach
                </select>
             </div>
            <select id="rubric-available-posts" multiple wire:model="selectedAvailablePosts"
                    class="form-control flex-fill" size="8">
              @foreach($availablePosts as $post)
                <option value="{{ $post->id }}">
                    {{ $post->title }}
                </option>
              @endforeach
            </select>
        </div>
    </div>
</div>
