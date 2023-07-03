window.addEventListener('showModal', () => {
    const modal = new bootstrap.Modal('#modal')

    console.log("show")
    modal.show()
    document.getElementById('modal').addEventListener('hidden.bs.modal', () => Livewire.emitTo('modal-manager', 'unload'))
})
