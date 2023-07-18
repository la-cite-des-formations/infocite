<div class="dropend">
    <button wire:ignore.self id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false" type="button"
            class="btn btn-light choice-icon-btn dropdown-toggle">
        <span class="material-icons mr-1 mt-1">{{ (!($$model->icon) ? '...' : $$model->icon) }}</span>
    </button>
    <div wire:ignore.self class="dropdown-menu text-muted bg-icon-picker ms-1 p-1 border-0">
        <div class="input-group mb-1 border-0">
            <div class="input-group-text text-secondary border-0">
                <span class="material-icons">search</span>
            </div>
            <input wire:model='searchIcons' type="text" class="form-control search-icons border-0" placeholder="Rechercher...">
        </div>
        <div id="{{$model}}-icon" wire:model="{{$model}}.icon" type="input" style="max-width: 400px; max-height: 202px;"
             class="form-control d-flex flex-wrap overflow-auto justify-content-start p-0 border-0">
          @foreach($icons as $miName => $miCode)
            <button wire:click="choiceIcon('{{$miName}}', '{{$model}}')" type="button" value='{{ $miName }}' title='{{ $miName }}'
                    class='btn btn-sm {{ $$model->icon === $miName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
            </button>
          @endforeach
        </div>
    </div>
</div>
