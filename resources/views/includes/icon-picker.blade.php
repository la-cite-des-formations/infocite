<div wire:ignore.self id="icon-picker" class="col dropend">
    <button wire:ignore.self id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false" type="button"
            class="btn btn-light choice-icon-btn dropdown-toggle">
        <span class="material-icons me-1 mt-1">{{ $$model->icon ?: '...' }}</span>
    </button>
    <a href="https://fonts.google.com/icons?icon.set=Material+Icons"
       type="button" class="ms-1" target="_blank"
       title="Sur Google Fonts, copier le nom ou bien le code de votre icône ; puis sur Infocité, coller-le dans la zone de recherche du selecteur.">
        <span class="material-icons align-text-bottom">help</span>
    </a>
    <div wire:ignore.self class="dropdown-menu text-muted bg-icon-picker ms-1 p-1 border-0">
        <div class="input-group mb-1 border-0">
            <div class="input-group-text text-secondary border-0">
                <span class="material-icons">search</span>
            </div>
            <input wire:model='searchIcons' type="text" class="form-control search-icons border-0" placeholder="Rechercher...">
        </div>
        <div id="{{$model}}-icon" wire:model="{{$model}}.icon" type="input" style="max-width: 400px; max-height: 202px;"
             class="form-control d-flex flex-wrap overflow-auto justify-content-start p-0 border-0">
         @if (AP::getRecentMiCodes()->isNotEmpty())
          @foreach (AP::getRecentMiCodes() as $recentMiName => $recentMi)
            <button wire:click="choiceIcon('{{$recentMiName}}', '{{$model}}')" type="button" value='{{ $recentMiName }}' title='{{ $recentMiName }}'
                    class='btn btn-sm {{ $$model->icon === $recentMiName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                <span class='material-icons align-top'>{!! "&#x{$recentMi['code']};" !!}</span>
            </button>
          @endforeach
            <hr class="col-12 my-0">
         @endif
          @foreach($icons as $miName => $miCode)
            <button wire:click="choiceIcon('{{$miName}}', '{{$model}}')" type="button" value='{{ $miName }}' title='{{ $miName }}'
                    class='btn btn-sm {{ $$model->icon === $miName ? 'text-light choice-icon-div' : 'btn-outline-secondary' }} m-1 p-1 border-0'>
                <span class='material-icons align-middle'>{!! "&#x{$miCode};" !!}</span>
            </button>
          @endforeach
        </div>
    </div>
</div>
