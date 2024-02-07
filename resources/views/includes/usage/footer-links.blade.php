<div class="col-6 footer-links d-flex flex-wrap justify-content-around mt-4">
 @foreach ($rubrics as $rubric)
  @can('access', $rubric)
   @if($rubric->hasChilds())
    <div class="d-flex flex-column">
        <h4>{{ $rubric->name }}</h4>
        <ul>
          @foreach ($rubric->childs as $childRubric)
            <li>
                <i class="bx bx-chevron-right"></i>
                <a href="/{{ $childRubric->segment }}">{{ $childRubric->name }}</a>
            </li>
          @endforeach
        </ul>
    </div>
   @else
    <h4>
        <a href="/{{ $rubric->segment }}"
           @if($rubric->segment == 'dashboard') target="_blank" @endif>{{ $rubric->name }}</a>
    </h4>
   @endif
  @endcan
 @endforeach
</div>

