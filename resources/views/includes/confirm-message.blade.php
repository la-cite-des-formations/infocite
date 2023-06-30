<div class="row p-3">
    <div class="col-4 m-auto text-center mb-0 position-relative alert alert-{{ session('alertClass') }} alert-dismissible" role="alert">
        {!! session('message') !!}
        <button type="button" class="close bg-transparent position-absolute border-0 top-0 end-0 text-{{ session('alertClass') }} fs-5" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
