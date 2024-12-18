<div class="row" >
    <div class="col" id="paginationContainer">
        {{ $elements->links() }}
    </div>
    <div class="col text-muted">
        <div class="form-inline d-flex justify-content-end">
            <select wire:model="{{ $perPage }}" class="form-control-sm">
              @foreach($perPageOptions as $perPageOption)
                <option>{{ $perPageOption }}</option>
              @endforeach
            </select>
            &nbsp; résultats par page
        </div>
        <div class="text-end">
            de {{ $elements->firstItem() ?? 0 }} à {{ $elements->lastItem() ?? 0 }} sur {{ $elements->total() }}
        </div>
    </div>
</div>
