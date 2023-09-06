@extends('layouts.usage-modal')

@section('modal-title', "Notifications")
@section('modal-size', 'modal-lg')

@section('modal-body')
    <ul>
      @foreach ($newNotifications as $notification)
        <li class="alert alert-danger">{{ $notification->message }}
            <a wire:click='consumedNotif({{ $notification->id }})' href="{{ $notification->h_ref }}">{{ "{$notification->post->rubric->name}/{$notification->post->title}" }}</a>
        </li>
      @endforeach
      @foreach ($oldNotifications as $notification)
        <li class="alert alert-info">{{ $notification->message }}
            <a href="{{ $notification->h_ref }}">{{ "{$notification->post->rubric->name}/{$notification->post->title}" }}</a>
        </li>
      @endforeach
    </ul>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
@endsection
