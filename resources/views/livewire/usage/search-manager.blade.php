<div class="container separator mb-3">
    <p class="mb-1">Filtrer les articles :</p>
    <div class="sectionFilterUneButton">
      @foreach(AP::getUneFiltered() as $filterName => $atribute)
        <div class="filterUneButton"  >
            <input class="filterUne d-none" wire:model="filter.{{$atribute['name']}}"  type="radio"
                    id="{{$atribute['name']}}" name="filter" />
            <label class="@if($filter[$filterName]) colorFilterUneEnabled @else colorFilterUneDisabled @endif"
                    for="{{$atribute['name']}}" role="button">
                <span class="material-icons fs-4">{{$atribute['icone']}}</span>
                {{$atribute['libelle']}}
            </label>
        </div>
      @endforeach
    </div>
    <p class="mb-1">Trier les articles :</p>
    <div class="sectionFilterUneButton">
      @foreach(AP::getUneSorted() as $sorterName => $atribute)
        <div class="filterUneButton">
            <input class="filterUne d-none" wire:model="sorter.{{$atribute['name']}}" type="radio"
                    id="{{$atribute['name']}}" name="filter"/>
            <label class="@if($sorter[$sorterName]) colorFilterUneEnabled @else colorFilterUneDisabled @endif"
                    for="{{$atribute['name']}}" role="button">
                <span class="material-icons fs-4">{{$atribute['icone']}}</span>
                {{$atribute['libelle']}}</label>
        </div>
      @endforeach
    </div>
</div>
