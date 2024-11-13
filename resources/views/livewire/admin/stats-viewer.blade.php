@extends('layouts.card')

@section('card-title', AP::getDashboardFunction($models, isset($dashboard) ? $dashboard : 'main')->table_title)

@section('card-body')
    {{-- @include("includes.admin.{$models}.table") --}}
@endsection

@section('card-footer')
  {{-- @if($$elements->count())
    @include('includes.pagination', ['elements' => $$elements])
  @else
    <div class="alert alert-warning">Aucun résultat correspondant à l'état actuel du filtre.</div>
  @endif --}}
@endsection
