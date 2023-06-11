window.addEventListener('showModal', () => {
    $('#modal').modal('show')
    $('#modal').on('hidden.bs.modal', () => Livewire.emitTo('modal-manager', 'unload'))
})
