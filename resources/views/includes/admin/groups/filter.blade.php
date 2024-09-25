<!-- filtre ... -->
<div>
    <div class="input-group">
        <span class="input-group-text text-secondary material-icons md-18" title="Type de groupe">category</span>
        <select wire:model='filter.type' class="form-select" id="group-type-filter">
            <option label="Choisir un type de groupe..."></option>
          @foreach (AP::getGroupTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
</div>
