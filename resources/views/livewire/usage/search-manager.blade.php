<div>
        @foreach(AP::getUneFilter() as $filterName => $atribute)
            <div>
                <input wire:model="filter.{{$atribute['name']}}" type="radio" id="{{$atribute['name']}}" name="filter"/>
                <label for="{{$atribute['name']}}">{{$atribute['libelle']}}</label>
            </div>

        @endforeach


</div>
