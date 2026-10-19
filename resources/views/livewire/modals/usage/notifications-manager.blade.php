@extends('layouts.modal')

@section('modal-title', 'Notifications')
@section('modal-size', 'modal-lg')

@section('modal-body')
    <ul>
        @foreach ($newNotifications as $notification)
            <li class="alert alert-danger">{{ $notification->message }}
                @if ($notification->content_type === 'RP')
                    <a href="{{ $notification->h_ref }}">Accéder à mes articles concernés</a>
                @elseif ($notification->content_type === 'DF')
                    <span>Pensez aux modèles d'articles lors de la rédaction ou révision de vos contenus.</span>
                    <a href="{{ $notification->h_ref }}" wire:click.prevent="openTemplateReminder({{ $notification->id }})">Accéder à la Une</a>
                @else
                    <a wire:click='consumedNotif({{ $notification->id }})'
                        href="{{ $notification->h_ref }}">{{ $notification->object->identity() }}</a>
                @endif
            </li>
        @endforeach
        @foreach ($oldNotifications as $notification)
            <li class="alert alert-info">{{ $notification->message }}
                @if ($notification->content_type === 'RP')
                    <a href="{{ $notification->h_ref }}">Accéder à mes articles concernés</a>
                @elseif ($notification->content_type === 'DF')
                    <span>Pensez aux modèles d'articles lors de la rédaction ou révision de vos contenus.</span>
                    <a href="{{ $notification->h_ref }}" wire:click.prevent="openTemplateReminder({{ $notification->id }})">Accéder à la Une</a>
                @else
                    <a href="{{ $notification->h_ref }}">{{ $notification->object->identity() }}</a>
                @endif
            </li>
        @endforeach
    </ul>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
@endsection
