<!-- filtre ... -->
<div class="pl-0 col-6">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Type de groupe">
                <span class="material-icons md-18">category</span>
            </div>
        </div>
        <select wire:model='filter.groupType' class="form-control" id="user-groupType-filter">
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
        <select wire:model='filter.groupId' class="form-control" id="user-groupId-filter">
            <option label="{{ $groupFilter['choiceLabel'] }}"></option>
          @foreach($groups as $group)
            <option value='{{ $group->id }}'>{{ $group->name }}</option>
          @endforeach
        </select>
    </div>
  @endif
    <div class="input-group mt-3">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Type de comptes">
                <span class="material-icons md-18">person</span>
            </div>
        </div>
        <select wire:model='filter.isFrozen' class="form-control" id="user-isFrozen-filter">
            <option label='Choisir un type de comptes...'></option>
            <option value='0'>Comptes actifs</option>
            <option value='1' class="alert-warning">Comptes inactifs</option>
        </select>
    </div>
</div>
