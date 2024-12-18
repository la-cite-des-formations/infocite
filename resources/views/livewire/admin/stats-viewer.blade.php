@extends('layouts.card')

@section('card-title', AP::getDashboardFunction($statsPage, isset($dashboard) ? $dashboard : 'main')->table_title)

@section('card-body')
    <div wire:init="drawAllCharts()">
      @foreach (array_keys($statsCollection) as $statsName)
        @include("includes.admin.stats.{$statsName}")
      @endforeach
    </div>
@endsection

@section('card-footer')
@endsection
