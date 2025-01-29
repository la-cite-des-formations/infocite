<div class="row p-3">
    <div class="col-4 m-auto text-center mb-0 position-relative alert alert-{{ session('alertClass') }} alert-dismissible" role="alert">
        {!! session('message') !!}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
