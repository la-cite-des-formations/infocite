<div class="input-group mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text text-secondary rounded-top-0" title="Rechercher un utilisateur">
            <span class="material-icons md-18">search</span>
        </div>
    </div>
    <input wire:model="userSearch" class="form-control rounded-top-0" id="right-searched-user">
</div>
<select
    id="right-to-attach-users"
    multiple wire:model="selectedToAttachUsers"
    class="form-control flex-fill"
    size="4">
  @foreach($attachableRightables as $user)
    <option value="{{ $user->id }}">
        {{ $user->identity() }}
    </option>
  @endforeach
</select>
<div class="form-group row mr-auto my-2">
    <label class="col-4 text-right my-auto" for="priority">Priorité</label>
    <input id="priority" wire:model="priority" type="input" class="col-8 form-control" placeholder="Ordre de priorité">
</div>
<div class="form-check my-2">
    <input wire:model="hasResource" class="form-check-input" type="checkbox" id="users-cbx-resource">
    <label for="users-cbx-resource">Associer une ressource</label>
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
