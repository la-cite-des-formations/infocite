window.addEventListener('showModal', event => {
    $('#modal').modal('show')
    $('#modal').on('hidden.bs.modal', () => Livewire.emitTo('modal-manager', 'unload'))
})
