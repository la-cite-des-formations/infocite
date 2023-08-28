@extends('layouts.usage-modal')

    @section('modal-title', "Notification")
    @section('modal-body')
        <div class="alert alert-danger mb-3">
            <ul>
              @foreach ($alertPosts as $post)
              {{-- @if (!($post->isRead())) --}}
                <li>{{ $post->title }}<i> ( depuis {{ $post->updated_at->format('d/m/Y') }} )</i></li>
              {{-- @endif --}}
                {{-- <div class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                    type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                    <i class="bx bx-message-alt-check"></i>
                </div> --}}
              @endforeach
            </ul>
        </div>
    @endsection


