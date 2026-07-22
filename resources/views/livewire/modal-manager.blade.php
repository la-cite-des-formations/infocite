<div>
    @if ($modal)
        @livewire("modals.$modal", [$data, $filter], key($modal . '-' . md5(json_encode($data))))
    @endif
</div>
