<div class="input-group mb-1">
    <div class="input-group-text text-secondary rounded-top-0" title="Rechercher un utilisateur">
        <span class="material-icons md-18">search</span>
    </div>
    <input wire:model="userSearch" class="form-control rounded-top-0" id="right-searched-user">
</div>
<select id="right-to-attach-users" multiple wire:model="selectedToAttachUsers"
    class="form-select flex-fill mb-2" size="4">
  @foreach($attachableRightables as $user)
    <option value="{{ $user->id }}">
        {{ $user->identity() }}
    </option>
  @endforeach
</select>
<div class="row g-2 align-items-center mb-2">
    <label class="col-5 col-form-label text-end" for="priority">Priorité</label>
    <div class="col">
        <input id="priority" wire:model="priority" type="input" class="form-control" placeholder="Ordre de priorité">
    </div>
</div>
<div class="row g-2 align-items-center mb-2">
    <label class="col-5 form-check-label text-end" for="users-cbx-resource">Lier une ressource</label>
    <div class="form-check col ms-1">
        <input wire:model="hasResource" class="form-check-input" type="checkbox" id="users-cbx-resource">
    </div>
</div>
@if ($hasResource)
<div class="row g-2 align-items-center mb-2">
    <label class="col-4 col-form-label text-end" for="resource-type">Type</label>
    <div class="col">
        <select id="resource-type" wire:model="resourceType" type="input" class="form-select">
            <option label="Choisir le type de ressource..."></option>
          @foreach(AP::getResourceables() as $key => $type)
            <option value='{{ $key }}'>{{ $type }}</option>
          @endforeach
        </select>
    </div>
</div>
@endif
@if (!empty($resourceType) && $hasResource)
<div class="row g-2 align-items-center mb-2">
    <label class="col-4 col-form-label text-end" for="resource-id">Ressource</label>
    <div class="col">
        <select id="resource-id" wire:model="resourceId" type="input" class="form-select">
            <option label="Choisir la ressource..."></option>
          @foreach($resourceables as $resource)
            <option value='{{ $resource->id }}'>{{ $resource->identity() }}</option>
          @endforeach
        </select>
    </div>
</div>
@endif
