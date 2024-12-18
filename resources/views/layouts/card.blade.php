<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="card-title">@yield('card-title')</h3>
            </div>
            @includeWhen(isset($filter), 'includes.search-filter')
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
