<div class="dropend">
    <button wire:ignore.self id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false" type="button" class="btn btn-light choice-icon-btn dropdown-toggle">
        <span class="material-icons me-1 mt-1">{{$post->icon}}</span>
    </button>
    <div wire:ignore.self class="dropdown-menu text-muted bg-icon-picker ms-1 p-3"
        style="max-width: 436px;">
        <div class="input-group mb-2">
            <div class="input-group-text text-secondary">
                <span class="material-icons">search</span>
            </div>
            <input wire:model='searchIcons' type="text" class="form-control" placeholder="Rechercher...">
        </div>
        <div id="post-icon" wire:model="post.icon" type="input"
             class="form-control d-flex flex-wrap
                    overflow-auto justify-content-start p-0"
             style="max-height: 204px;">
          @foreach($icons as $miName => $miCode)
            <button wire:click="choiceIcon('{{$miName}}')" class='border-0 btn btn-sm {{ $post->icon === $miName ? 'btn-outline-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 ' type="button" value='{{ $miName }}' title='{{ $miName }}'>
                <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
            </button>
          @endforeach
        </div>
    </div>
</div>
