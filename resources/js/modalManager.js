window.addEventListener('showModal', event => {
    $('#modal').modal('show')
    $('#modal').on('hidden.bs.modal', () => Livewire.emitTo('modal-manager', 'unload'))
})

window.addEventListener('autoOpenGuideline', event => {
    const contextKey = event.detail ? event.detail.contextKey : null
    if (contextKey) {
        Livewire.emitTo('modal-manager', 'show', {
            component: 'usage.guidelines-manager',
            data: { contextKey: contextKey }
        })
    }
})
