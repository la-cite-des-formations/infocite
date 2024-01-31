@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">comment</span>
            </div>
            <div class="my-auto">
                <h5>{{ $comment->author->identity() }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <dl class="row mb-0 mx-0">
            <!-- Commentaire ... -->
            <dd class="col-12 pl-0">{{ $comment->content }}</dd>
            <!-- Article -->
            <dt class="col-3 text-right pl-0">Article</dt>
            <dd class="col-9 pl-0 text-truncate">{{ $comment->post->title }}</dd>
            <!-- Description ... -->
            <dt class="col-3 text-right pl-0">Date</dt>
            <dd class="col-9 pl-0">{{ $comment->created_at }}</dd>
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
