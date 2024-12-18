@extends('layouts.card')

@section('card-title', AP::getDashboardFunction($models, isset($dashboard) ? $dashboard : 'main')->table_title)
@if(!$filter['searchOnly'])
  @section('add-filter')
    <div class="collapse @if($showFilter) show @endif" id="filter">
        <hr/>
        <div class="col-9 px-0 d-flex justify-content-between">
            @include("includes.admin.{$models}.filter")
        </div>
    </div>
  @endsection
@endif
@section('card-body')
    @include("includes.admin.{$models}.table")
@endsection
@section('card-footer')
  @if($$elements->count())
    @include('includes.pagination', ['elements' => $$elements, 'perPage' => 'perPage'])
  @else
    <div class="alert alert-warning">Aucun résultat correspondant à l'état actuel du filtre.</div>
  @endif
@endsection
