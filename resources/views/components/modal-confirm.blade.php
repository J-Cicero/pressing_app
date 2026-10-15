<!-- Reusable Modal Confirmation & Alert Component (Zero Browser confirm()) -->
<div
    id="global-confirm-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-modal-title"
    aria-describedby="confirm-modal-message"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-xs transition-opacity duration-200 hidden opacity-0"
    onclick="handleModalBackdropClick(event)"
>
    <div
        id="confirm-modal-card"
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5 transform transition-all duration-200 scale-95"
    >
        <div class="flex items-start gap-4">
            <!-- Icon Indicator -->
            <div id="confirm-modal-icon-container" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                <svg id="confirm-modal-icon-danger" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <svg id="confirm-modal-icon-info" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg id="confirm-modal-icon-warning" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Content -->
            <div class="space-y-1.5 min-w-0 flex-1">
                <h3 id="confirm-modal-title" class="text-sm font-black text-zinc-950 dark:text-white tracking-tight">
                    Confirmation requise
                </h3>
                <p id="confirm-modal-message" class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Êtes-vous sûr de vouloir exécuter cette action ?
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
            <button
                type="button"
                id="confirm-modal-cancel-btn"
                onclick="closeConfirmModal()"
                class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition"
            >
                Annuler
            </button>
            <button
                type="button"
                id="confirm-modal-confirm-btn"
                class="px-4 py-2 rounded-xl text-xs font-bold bg-black dark:bg-white text-white dark:text-black hover:opacity-90 transition shadow-xs flex items-center gap-1.5"
            >
                <span id="confirm-modal-btn-label">Confirmer</span>
            </button>
        </div>
    </div>
</div>

<script>
    let activeConfirmCallback = null;
    let activeConfirmForm = null;

    function showConfirmModal(options = {}) {
        const modal = document.getElementById('global-confirm-modal');
        const card = document.getElementById('confirm-modal-card');
        const titleEl = document.getElementById('confirm-modal-title');
        const msgEl = document.getElementById('confirm-modal-message');
        const btnLabel = document.getElementById('confirm-modal-btn-label');
        const confirmBtn = document.getElementById('confirm-modal-confirm-btn');
        const cancelBtn = document.getElementById('confirm-modal-cancel-btn');

        const iconDanger = document.getElementById('confirm-modal-icon-danger');
        const iconInfo = document.getElementById('confirm-modal-icon-info');
        const iconWarning = document.getElementById('confirm-modal-icon-warning');

        // Set Texts
        titleEl.textContent = options.title || 'Confirmation requise';
        msgEl.textContent = options.message || 'Êtes-vous sûr de vouloir continuer ?';
        btnLabel.textContent = options.confirmText || 'Confirmer';
        cancelBtn.textContent = options.cancelText || 'Annuler';

        // Icon handling
        const type = options.type || 'danger';
        iconDanger.classList.add('hidden');
        iconInfo.classList.add('hidden');
        iconWarning.classList.add('hidden');

        if (type === 'danger') {
            iconDanger.classList.remove('hidden');
            confirmBtn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs flex items-center gap-1.5";
        } else if (type === 'info') {
            iconInfo.classList.remove('hidden');
            confirmBtn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs flex items-center gap-1.5";
        } else {
            iconWarning.classList.remove('hidden');
            confirmBtn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs flex items-center gap-1.5";
        }

        // Store target action
        activeConfirmCallback = typeof options.onConfirm === 'function' ? options.onConfirm : null;
        activeConfirmForm = options.form || null;

        // Display Modal
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
            cancelBtn.focus();
        });
    }

    function closeConfirmModal() {
        const modal = document.getElementById('global-confirm-modal');
        const card = document.getElementById('confirm-modal-card');

        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        modal.classList.add('opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            activeConfirmCallback = null;
            activeConfirmForm = null;
        }, 180);
    }

    function handleModalBackdropClick(event) {
        if (event.target === document.getElementById('global-confirm-modal')) {
            closeConfirmModal();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const confirmBtn = document.getElementById('confirm-modal-confirm-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                if (activeConfirmCallback) {
                    activeConfirmCallback();
                } else if (activeConfirmForm) {
                    activeConfirmForm.submit();
                }
                closeConfirmModal();
            });
        }

        // Support Escape key to dismiss modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modal = document.getElementById('global-confirm-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeConfirmModal();
                }
            }
        });
    });
</script>
