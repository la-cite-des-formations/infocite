<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-8">
                <h3 class="card-title">@yield('card-title')</h3>
            </div>
            @include('includes.search-filter')
        </div>
        @yield('add-filter')
    </div>
    <div class="card-body mx-2">
        @yield('card-body')
    </div>
    <div class="card-footer">
        @yield('card-footer')
    </div>
</div>
