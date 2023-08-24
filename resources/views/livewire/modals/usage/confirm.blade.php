@extends('layouts.usage-modal')

  @if ($handling === 'notification')
    @section('modal-title', "Notification")
    @section('modal-body')
        <div class="alert alert-danger mb-3">
            <ul>
              @foreach ($alertPosts as $post)
              @if (!($post->isRead()))
                <li>{{ $post->title }}<i> ( depuis {{ $post->updated_at->format('d/m/Y') }} )</i></li>
              @endif
                {{-- <div class="input-group-text @if ($post->isRead()) btn-success @else btn-danger @endif btn-sm"
                    type="text" @if ($post->isRead()) title="Déjà consulté" @else title="À consulter" @endif>
                    <i class="bx bx-message-alt-check"></i>
                </div> --}}
              @endforeach
            </ul>
        </div>
    @endsection

    @section('modal-footer')
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
    @endsection
  @else
    @section('modal-title', "Confirmation")

@section('modal-body')
    <div class="alert alert-{{ $handling === 'update' || $handling === 'create' ? 'success' : 'danger' }} mb-3">
        <p>{{$message}}</p>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-{{ $handling === 'update' || $handling === 'create' ? 'primary' : 'danger' }}" wire:click="confirm" data-bs-dismiss="modal">OK</button>
@endsection
  @endif
