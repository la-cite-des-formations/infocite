@extends('layouts.card')

@section('card-title', AP::getDashboardFunction($statsPage, isset($dashboard) ? $dashboard : 'main')->table_title)

@section('card-body')
    <div wire:init="drawAllCharts()">
        @include('includes.tabs', ['tabsSystem' => $chartTabs])
    </div>
@endsection

@section('card-footer')
@endsection
