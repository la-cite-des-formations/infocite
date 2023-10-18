@extends('layouts.admin-app')

@section('content')
<div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="w-100 h-100">
            <div class="row justify-content-center">
              @foreach (AP::getDashboardFunctions($dashboard) as $function)
               @can(is_array($function->gate) ? $function->gate['name'] : $function->gate,
                    is_array($function->gate) ? $function->gate['dashboard'] : NULL)
                <div class="m-3 rounded bg-light">
                    <div class="card card-dashboard card-dashboard-{{ $function->color }} text-center m-0">
                        <span class="material-icons-outlined md-48 db-text-{{ $function->color }}">{{ $function->icon_name}}</span>
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold">{{ $function->title }}</h5>
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
        </div>
    </div>
</div>
@endsection
