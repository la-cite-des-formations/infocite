@extends('layouts.modal')

@section('modal-title', "Guide")

@section('modal-body')
    @include("includes.guidelines.{$subject}", $data)
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
@endsection
