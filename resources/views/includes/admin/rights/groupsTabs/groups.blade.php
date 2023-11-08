<div class="input-group mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text text-secondary rounded-top-0" title="Rechercher un groupe">
            <span class="material-icons md-18">search</span>
        </div>
    </div>
    <input wire:model="groupSearch" class="form-control rounded-top-0" id="right-searched-group">
</div>
<select
    id="right-to-attach-groups"
    multiple wire:model="selectedToAttachGroups"
    class="form-control flex-fill"
    size="4">
  @foreach($attachableRightables as $group)
    <option value="{{ $group->id }}">
        {{ $groupType != 'all' ? $group->name : $group->identity() }}
    </option>
  @endforeach
</select>
<div class="form-group row mr-auto my-2">
    <label class="col-4 text-right my-auto" for="priority">Priorité</label>
    <input id="priority" wire:model="priority" type="input" class="col-8 form-control" placeholder="Ordre de priorité">
</div>
<div class="form-check my-2">
    <input wire:model="hasResource" class="form-check-input" type="checkbox" id="groups-cbx-resource">
    <label for="groups-cbx-resource">Associer une ressource</label>
</div>
@if ($hasResource)
<div class="form-group row mr-auto my-2">
    <label class="col-4 text-right my-auto" for="resource-type">Type</label>
    <select id="resource-type" wire:model="resourceType" type="input" class="col-8 form-control">
        <option label="Choisir le type de ressource..."></option>
      @foreach(AP::getResourceables() as $key => $type)
        <option value='{{ $key }}'>{{ $type }}</option>
      @endforeach
    </select>
</div>
@endif
@if (!empty($resourceType) && $hasResource)
<div class="form-group row mr-auto my-2">
    <label class="col-4 text-right my-auto" for="resource-id">Ressource</label>
    <select id="resource-id" wire:model="resourceId" type="input" class="col-8 form-control">
        <option label="Choisir la ressource..."></option>
      @foreach($resourceables as $resource)
        <option value='{{ $resource->id }}'>{{ $resource->identity() }}</option>
      @endforeach
    </select>
</div>
@endif
