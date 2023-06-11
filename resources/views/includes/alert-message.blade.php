<div class="row p-3">
    <div class="col mb-0 alert alert-{{ session('alertClass') }} alert-dismissible" role="alert">
        {!! session('message') !!}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
