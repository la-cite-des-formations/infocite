<div>
    <div class="container separator mb-2">
        <p class="mb-1">Filtrer les article par : </p>
        <div class="sectionFilterUneButton">
            @foreach(AP::getUneFilter() as $filterName => $atribute)
                <div class="filterUneButton">
                    <input class="filterUne d-none" wire:model="filter.{{$atribute['name']}}" type="radio"
                           id="{{$atribute['name']}}" name="filter"/>
                    <label class=" @if($filter[$filterName]) colorFilterUneEnabled @else colorFilterUneDisabled @endif"
                           for="{{$atribute['name']}}">{{$atribute['libelle']}}</label>
                </div>
            @endforeach
        </div>
        <p class="mb-1">Trier les article par :</p>
        <div class="sectionFilterUneButton">
            @foreach(AP::getUneSorted() as $sorterName => $atribute)
                <div class="filterUneButton">
                    <input class="filterUne d-none" wire:model="sorter.{{$atribute['name']}}" type="radio"
                           id="{{$atribute['name']}}" name="filter"/>
                    <label class="@if($sorter[$sorterName]) colorFilterUneEnabled @else colorFilterUneDisabled @endif"
                           for="{{$atribute['name']}}">{{$atribute['libelle']}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <br>
</div>
