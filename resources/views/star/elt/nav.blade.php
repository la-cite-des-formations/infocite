<nav>
    <ul class="liste">
        @foreach($rubrics as $rubric)
        <li @if ($rubric->hasChilds()) class="dropdown" role="button" @endif>
            @if ($rubric->hasChilds())
            <div class="flex service">
                <h3>{{ $rubric->name }}</h3>
                <i class="bi bi-chevron-down"></i>
            </div>
            <ul class="sous">
                @foreach ($rubric->childs as $childRubric)
                <li>
                    <a href="/star/{{$rubric->segment}}.{{ $childRubric->segment }}" class="nav-link">
                        <h4>{{ $childRubric->name }}</h4>
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
        

    </ul>
</nav>

<style>
    nav {
        color: white;
        width: 80%;
        height: 100%;
    }

    nav ul {
        width: 100%;
        list-style-type: none;
        
    }

    .liste{
        display: flex;
        justify-content: start;
    }

    nav ul li {
        float: left;
        text-align: center;
        position: relative;
    }

    nav ul::after {
        content: "";
        display: table;
        clear: both;
    }

    nav a {
        display: block;
        text-decoration: none;
        color: #212529;
    }

    .sous {
        display: none;
        position: absolute;
        width: fit-content;
        z-index: 1000;
        background-color: #EAEAEA;
    }

    nav>ul>li:hover,
    li:hover .sous, li:hover .sous a {
        display: block;
        background-color: #EAEAEA;
        color: #212529;
    }

    .sous li {
        float: none;
        width: 100%;
        text-align: left;
        padding-inline: 20px;
    }

    .sous li:hover a {
        color: #109e92;
        /* background-color: #212529; */

    }

    .service {
        padding: 10px;
    }

    .service h3{
        margin-right: 5px;
    }
</style>