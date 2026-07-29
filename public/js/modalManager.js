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

    const isAlreadyOpen = modalElement.classList.contains('show');

    // Récupérer l'instance existante ou en créer une nouvelle
    let modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalElement);
    }

    if (!isAlreadyOpen) {
        modalInstance.show();
    }

    if (!modalElement.dataset.listenerAdded) {
        modalElement.addEventListener('hidden.bs.modal', () => {
            const needsRefresh = modalElement.dataset.needsRefresh === 'true';
            Livewire.emitTo('modal-manager', 'unload');
            if (needsRefresh) {
                Livewire.emit('refreshPage');
                delete modalElement.dataset.needsRefresh;
            }
            forceCleanup();
        });
        modalElement.dataset.listenerAdded = 'true';
    }
});

// Drapeau posé par le serveur quand une action nécessite un rafraîchissement de la page
window.addEventListener('mark-needs-refresh', () => {
    const modalElement = document.getElementById('modal');
    if (modalElement) {
        modalElement.dataset.needsRefresh = 'true';
    }
});

window.addEventListener('close-modal', closeModal);
window.addEventListener('hideModal', closeModal);

window.addEventListener('autoOpenGuideline', event => {
    console.log('[autoOpenGuideline] Événement navigateur reçu:', event.detail);
    const contextKey = event.detail ? event.detail.contextKey : null;
    if (contextKey) {
        console.log('[autoOpenGuideline] Émission Livewire.emitTo pour contextKey:', contextKey);
        Livewire.emitTo('modal-manager', 'show', {
            component: 'usage.guidelines-manager',
            data: { contextKey: contextKey }
        });
    }
});
