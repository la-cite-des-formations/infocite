@extends('star.app')

@section('tabSubtitle', " : Star")

@section ('content')
<h1>Star</h1>
<div class="flex">
    

    @foreach($rubrics as $rubric)
    <div @if ($rubric->hasChilds()) class="dropdown" role="button" @endif> <!--changer hasChild pour isParent-->
        @if ($rubric->hasChilds())
        <div class="flex service">
            <h3>{{ $rubric->name }}</h3>
        </div>
        <ul class="">
            @foreach ($rubric->childs as $childRubric)
            <li>
                <a href="/star/{{$rubric->segment}}.{{ $childRubric->segment }}" class="nav-link">
                    <h4>{{ $childRubric->name }}</h4>
                </a>
            </li>
            @endforeach
        </ul>
        @else
        <div class="">
            <h3>{{ $rubric->name }}</h3>
        </div>
        @endif
    </div>
    @endforeach
</div>

<style>
    h1 {
        text-align: center;
    }
</style>

@endsection