@extends('layouts.admin-app')

@section('content')
<div class="container d-flex flex-wrap justify-content-center">
  @foreach (AP::getDashboardFunctions($dashboard) as $function)
   @can(is_array($function->gate) ? $function->gate['name'] : $function->gate,
        is_array($function->gate) ? $function->gate['dashboard'] : NULL)
    <div class="rounded bg-light m-3">
        <div class="card card-dashboard card-dashboard-{{ $function->color }} text-center m-0">
            <span class="material-icons-outlined md-48 db-text-{{ $function->color }}">{{ $function->icon_name}}</span>
            <div class="card-body">
                <h5 class="card-title fw-bold">{{ $function->title }}</h5>
                <p class="card-text">{{ $function->description }}</p>
            </div>
            @if($function->route)
            <a href="{{ route($function->route['name'], $function->route['parameters']) }}" class="stretched-link"></a>
            @endif
        </div>
    </div>
   @endcan
  @endforeach
</div>
@endsection
