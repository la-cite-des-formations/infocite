<div class="row p-3">
    <div class="col mb-0 alert alert-{{ session('alertClass') }} alert-dismissible" role="alert">
        {!! session('message') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
