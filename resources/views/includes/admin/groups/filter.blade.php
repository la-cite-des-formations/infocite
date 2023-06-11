<!-- filtre ... -->
<div>
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Type de groupe">
                <span class="material-icons md-18">category</span>
            </div>
        </div>
        <select wire:model='filter.type' class="form-control" id="group-type-filter">
            <option label="Choisir un type de groupe..."></option>
          @foreach (AP::getGroupTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
</div>
