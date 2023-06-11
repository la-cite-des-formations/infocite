<div wire:ignore.self @yield('wire-init') class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-dialog-scrollable @yield('modal-position') @yield('modal-size')">
        <div class="modal-content">
            <div class="modal-header card-header">
                <h5 class="modal-title">@yield('modal-title')</h5>
                <div class="btn-group">
                    @yield('modal-header-options')
                    <a role="button" class="text-secondary" data-dismiss="modal" aria-label="Fermer" title="Fermer">
                        <span class="material-icons">close</span>
                    </a>
                </div>
            </div>
            <div class="modal-body @yield('body-bg-color') @yield('body-m') @yield('body-p')">
                @yield('modal-body')
            </div>
            <div class="modal-footer">
                @yield('modal-footer')
            </div>
        </div>
    </div>
</div>
