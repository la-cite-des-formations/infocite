<!-- filtre ... -->
<div class="pl-0 col-6">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Type de groupe">
                <span class="material-icons md-18">category</span>
            </div>
        </div>
        <select wire:model='filter.groupType' class="form-control" id="profile-groupType-filter">
            <option label='Choisir un type de groupe...'></option>
          @foreach(AP::getGroupTypes() as $typeKey => $typeName)
            <option value='{{ $typeKey }}'>{{ $typeName }}</option>
          @endforeach
        </select>
    </div>
  @if($filter['groupType'])
    <div class="input-group mt-1">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Groupe">
                <span class="material-icons md-18">{{ $groupFilter['icon'] }}</span>
            </div>
        </div>
        <select wire:model='filter.groupID' class="form-control" id="profile-groupID-filter">
            <option label="{{ $groupFilter['choiceLabel'] }}"></option>
          @foreach($groups as $group)
            <option value='{{ $group->id }}'>{{ $group->name }}</option>
          @endforeach
        </select>
    </div>
  @endif
</div>
