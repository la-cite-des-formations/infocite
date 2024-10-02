@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex align-items-center">
            <span class="mx-2 material-icons-outlined md-36">comment</span>
            <div class="ms-1 my-auto">
                <h5 class="my-auto">{{ $comment->author->identity() }}</h5>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-3">
        <dl class="row m-0">
            <!-- Commentaire ... -->
            <dd class="col-12 ps-0">{{ $comment->content }}</dd>
        </dl>
    </div>
    <div class="alert alert-warning mb-0">
        <dl class="row m-0">
            <!-- Article -->
            <dt class="col-2 text-end ps-0">Article</dt>
            <dd class="col-10 ps-0 text-truncate">{{ $comment->post->title }}</dd>
            <!-- Description ... -->
            <dt class="col-2 text-end ps-0">Date</dt>
            <dd class="col-10 ps-0">{{ $comment->created_at }}</dd>
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
@endsection
