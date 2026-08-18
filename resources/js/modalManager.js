window.addEventListener('showModal', event => {
    const modalElement = document.getElementById('modal');
    if (!modalElement) return;
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    let modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.show();
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
