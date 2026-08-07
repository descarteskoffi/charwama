/**
 * Admin Categories Management
 * Gestion moderne des catégories avec validation et accessibilité
 * @version 2.0
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        MAX_NAME_LENGTH: 100,
        MIN_NAME_LENGTH: 2,
        MAX_ORDER: 999,
        MIN_ORDER: 0
    };

    // Éléments DOM
    const elements = {
        modal: null,
        openBtn: null,
        closeBtn: null,
        cancelBtn: null,
        form: null,
        nameInput: null,
        orderInput: null,
        statutCheckbox: null,
        submitBtn: null
    };

    /**
     * Initialisation au chargement du DOM
     */
    function init() {
        // Récupération des éléments
        elements.modal = document.getElementById('categoryModal');
        elements.openBtn = document.getElementById('openModalBtn');
        elements.closeBtn = document.getElementById('closeModalBtn');
        elements.cancelBtn = document.getElementById('cancelModalBtn');
        elements.form = document.getElementById('categoryForm');
        elements.nameInput = document.getElementById('categoryName');
        elements.orderInput = document.getElementById('categoryOrder');
        elements.statutCheckbox = document.getElementById('categoryStatut');
        elements.submitBtn = document.getElementById('submitCategoryBtn');

        // Vérification des éléments critiques
        if (!elements.modal || !elements.openBtn || !elements.form) {
            console.error('Éléments de modale catégorie manquants');
            return;
        }

        // Attachement des événements
        attachEventListeners();
        
        // Setup validations
        setupValidation();

        console.log('✓ Module admin-categories initialisé');
    }

    /**
     * Attache tous les event listeners
     */
    function attachEventListeners() {
        // Ouverture de la modale
        elements.openBtn.addEventListener('click', openModal);

        // Fermeture de la modale
        elements.closeBtn?.addEventListener('click', closeModal);
        elements.cancelBtn?.addEventListener('click', closeModal);

        // Fermer avec Escape
        document.addEventListener('keydown', handleEscapeKey);

        // Fermer en cliquant sur le fond
        elements.modal.addEventListener('click', handleBackdropClick);

        // Validation du formulaire
        elements.form.addEventListener('submit', handleFormSubmit);

        // Validation en temps réel
        elements.nameInput?.addEventListener('input', validateNameInput);
        elements.orderInput?.addEventListener('input', validateOrderInput);
    }

    /**
     * Configuration de la validation HTML5
     */
    function setupValidation() {
        if (elements.nameInput) {
            elements.nameInput.setAttribute('minlength', CONFIG.MIN_NAME_LENGTH);
            elements.nameInput.setAttribute('maxlength', CONFIG.MAX_NAME_LENGTH);
        }

        if (elements.orderInput) {
            elements.orderInput.setAttribute('min', CONFIG.MIN_ORDER);
            elements.orderInput.setAttribute('max', CONFIG.MAX_ORDER);
        }
    }

    /**
     * Ouvre la modale avec gestion du focus
     */
    function openModal(e) {
        if (e) e.preventDefault();

        elements.modal.style.display = 'flex';
        elements.modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Focus sur le premier champ
        setTimeout(() => {
            elements.nameInput?.focus();
        }, 100);

        // Trap focus dans la modale
        trapFocus(elements.modal);
    }

    /**
     * Ferme la modale
     */
    function closeModal(e) {
        if (e) e.preventDefault();

        elements.modal.style.display = 'none';
        elements.modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        // Reset du formulaire
        elements.form?.reset();
        clearValidationErrors();

        // Retour du focus au bouton d'ouverture
        elements.openBtn?.focus();
    }

    /**
     * Gestion de la touche Escape
     */
    function handleEscapeKey(e) {
        if (e.key === 'Escape' && elements.modal.style.display === 'flex') {
            closeModal();
        }
    }

    /**
     * Fermeture en cliquant sur le fond
     */
    function handleBackdropClick(e) {
        if (e.target === elements.modal) {
            closeModal();
        }
    }

    /**
     * Validation du nom de catégorie
     */
    function validateNameInput(e) {
        const input = e.target;
        const value = input.value.trim();
        let error = '';

        if (value.length < CONFIG.MIN_NAME_LENGTH && value.length > 0) {
            error = `Le nom doit contenir au moins ${CONFIG.MIN_NAME_LENGTH} caractères`;
        } else if (value.length > CONFIG.MAX_NAME_LENGTH) {
            error = `Le nom ne peut pas dépasser ${CONFIG.MAX_NAME_LENGTH} caractères`;
        }

        showInputError(input, error);
        return !error;
    }

    /**
     * Validation de l'ordre
     */
    function validateOrderInput(e) {
        const input = e.target;
        const value = parseInt(input.value, 10);
        let error = '';

        if (isNaN(value)) {
            error = 'L\'ordre doit être un nombre';
        } else if (value < CONFIG.MIN_ORDER) {
            error = `L\'ordre minimum est ${CONFIG.MIN_ORDER}`;
        } else if (value > CONFIG.MAX_ORDER) {
            error = `L\'ordre maximum est ${CONFIG.MAX_ORDER}`;
        }

        showInputError(input, error);
        return !error;
    }

    /**
     * Affiche une erreur de validation
     */
    function showInputError(input, message) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        // Supprimer l'ancienne erreur
        const oldError = formGroup.querySelector('.error-message');
        if (oldError) oldError.remove();

        if (message) {
            // Ajouter la nouvelle erreur
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            errorDiv.style.cssText = 'color: #ef4444; font-size: 0.75rem; margin-top: 4px;';
            formGroup.appendChild(errorDiv);

            input.style.borderColor = '#ef4444';
            input.setAttribute('aria-invalid', 'true');
        } else {
            input.style.borderColor = '';
            input.setAttribute('aria-invalid', 'false');
        }
    }

    /**
     * Supprime toutes les erreurs de validation
     */
    function clearValidationErrors() {
        const errors = elements.form?.querySelectorAll('.error-message');
        errors?.forEach(error => error.remove());

        const inputs = elements.form?.querySelectorAll('input');
        inputs?.forEach(input => {
            input.style.borderColor = '';
            input.removeAttribute('aria-invalid');
        });
    }

    /**
     * Gestion de la soumission du formulaire
     */
    function handleFormSubmit(e) {
        // Validation finale
        const isNameValid = validateNameInput({ target: elements.nameInput });
        const isOrderValid = validateOrderInput({ target: elements.orderInput });

        if (!isNameValid || !isOrderValid) {
            e.preventDefault();
            showToastError('Veuillez corriger les erreurs avant de soumettre');
            return false;
        }

        // Désactiver le bouton pour éviter les doubles soumissions (de manière asynchrone)
        if (elements.submitBtn) {
            setTimeout(function() {
                elements.submitBtn.disabled = true;
                elements.submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Création...';
            }, 10);
        }

        // Le formulaire se soumet normalement
        return true;
    }

    /**
     * Piège le focus dans la modale (accessibilité)
     */
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            if (e.key !== 'Tab') return;

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        });
    }

    /**
     * Affiche un toast d'erreur
     */
    function showToastError(message) {
        // Utilise la fonction toast si disponible
        if (typeof showToast === 'function') {
            showToast(message, 'error', 4000);
        } else {
            alert(message);
        }
    }

    // Initialisation au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
