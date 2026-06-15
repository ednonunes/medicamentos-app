let currentFormToSubmit = null;

// Anexamos à janela global para o onsubmit="openConfirmModal(...)" continuar funcionando
window.openConfirmModal = function(event, message) {
    event.preventDefault(); 
    currentFormToSubmit = event.target; 
    
    if(message) {
        document.getElementById('modal-message').textContent = message;
    }

    document.getElementById('global-confirm-modal').classList.remove('hidden');
}

window.closeConfirmModal = function() {
    const modal = document.getElementById('global-confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
    currentFormToSubmit = null;
}

// Inicializa os listeners assim que o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    const cancelBtn = document.getElementById('modal-cancel-btn');
    const confirmBtn = document.getElementById('modal-confirm-btn');

    if (cancelBtn) {
        cancelBtn.addEventListener('click', window.closeConfirmModal);
    }
    
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (currentFormToSubmit) {
                currentFormToSubmit.submit();
            }
            window.closeConfirmModal();
        });
    }
});