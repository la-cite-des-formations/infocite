<!-- filtre ... -->
<div class="pl-0 col-6">
    <div class="input-group mt-1">
        <div class="form-check">
            <input id="show-undefined-links" wire:model="filter.showUndefinedLinks"
                type="checkbox" class="form-check-input">
            <label class="my-auto" for="show-undefined-links">Afficher uniquement les liens à définir</label>
        </div>
    </div>

    <div class="input-group mt-1">
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary" title="Groupe">
                <span class="material-icons md-18">developer_board</span>
            </div>
        </div>
        <select wire:model='filter.groupId' class="form-control" id="user-groupId-filter">
            <option label="Choisir un processus..."></option>
          @foreach($processes as $process)
            <option value='{{ $process->group_id }}'>{{ $process->name }}</option>
          @endforeach
        </select>
    </div>
</div>
