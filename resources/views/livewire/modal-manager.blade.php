<div>
    @if ($modal)
        @livewire("modals.$modal", [$data, $filter], key($modal))
    @endif
</div>
