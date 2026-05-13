const forceCleanup = () => {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    [document.body, document.documentElement].forEach(el => {
        el.classList.remove('modal-open');
        el.style.overflow = '';
        el.style.paddingRight = '';
    });
};

const closeModal = () => {
    const modalElement = document.getElementById('modal');
    if (!modalElement) return;

    let modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
    forceCleanup();
};

window.addEventListener('showModal', () => {
    const modalElement = document.getElementById('modal');
    if (!modalElement) return;

    // Récupérer l'instance existante ou en créer une nouvelle
    let modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalElement);
    }

    modalInstance.show();

    if (!modalElement.dataset.listenerAdded) {
        modalElement.addEventListener('hidden.bs.modal', () => {
            Livewire.emitTo('modal-manager', 'unload');
            forceCleanup();
        });
        modalElement.dataset.listenerAdded = 'true';
    }
});

window.addEventListener('close-modal', closeModal);
window.addEventListener('hideModal', closeModal);
