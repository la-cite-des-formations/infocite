<!-- filtre ... -->
<div class="ps-0 col-6">
    <div class="input-group">
        <span class="input-group-text text-secondary material-icons md-18" title="Type de groupe">category</span>
        <select wire:model='filter.groupType' class="form-select" id="profile-groupType-filter">
            <option label='Choisir un type de groupe...'></option>
          @foreach(AP::getGroupTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
  @if($filter['groupType'])
    <div class="input-group mt-1">
        <span class="input-group-text text-secondary material-icons md-18" title="Groupe">{{ $groupFilter['icon'] }}</span>
        <select wire:model='filter.groupID' class="form-select" id="profile-groupID-filter">
            <option label="{{ $groupFilter['choiceLabel'] }}"></option>
          @foreach($groups as $group)
            <option value='{{ $group->id }}'>{{ $group->name }}</option>
          @endforeach
        </select>
    </div>
  @endif
</div>
