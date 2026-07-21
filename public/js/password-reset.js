/**
 * Password Reset - Utilitaires JavaScript
 * 
 * Gère :
 * - Soumission AJAX des formulaires de réinitialisation
 * - Cooldown avec compte à rebours
 * - Navigation OTP
 * - Alertes dynamiques
 */

const PasswordReset = (function () {
    'use strict';

    /**
     * Affiche une alerte dynamique dans un conteneur.
     * @param {HTMLElement} container - Le conteneur d'alertes
     * @param {string} type - Le type d'alerte (success, danger, warning, info)
     * @param {string} message - Le message à afficher
     * @param {number} [duration=5000] - Durée d'affichage en ms (0 = permanent)
     */
    function showAlert(container, type, message, duration = 5000) {
        if (!container) return;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} text-center alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        `;
        container.appendChild(alertDiv);

        if (duration > 0) {
            setTimeout(() => {
                alertDiv.classList.add('d-none');
            }, duration);
        }
    }

    /**
     * Vide le conteneur d'alertes.
     * @param {HTMLElement} container
     */
    function clearAlerts(container) {
        if (container) {
            container.innerHTML = '';
        }
    }

    /**
     * Échappe les caractères HTML pour prévenir les injections XSS.
     * @param {string} text
     * @returns {string}
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    /**
     * Envoie une requête AJAX avec Fetch API.
     * @param {string} url - L'URL de la requête
     * @param {string} method - La méthode HTTP
     * @param {Object} data - Les données à envoyer
     * @param {string} csrfToken - Le token CSRF
     * @returns {Promise<{status: number, data: Object}>}
     */
    function ajaxRequest(url, method, data, csrfToken) {
        const body = new URLSearchParams(data);

        return fetch(url, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: body,
        })
        .then(response => response.json().then(data => ({
            status: response.status,
            data: data,
        })));
    }

    /**
     * Configure le compte à rebours sur un bouton.
     * @param {HTMLElement} btn - Le bouton à désactiver
     * @param {HTMLElement} displayElement - L'élément où afficher le temps restant
     * @param {number} seconds - Le nombre de secondes du compte à rebours
     * @param {Function} onComplete - Callback appelé à la fin
     * @returns {number} L'ID du timer (pour annulation)
     */
    function startCountdown(btn, displayElement, seconds, onComplete) {
        btn.disabled = true;

        const timerId = setInterval(() => {
            if (seconds > 0) {
                displayElement.textContent = `Veuillez attendre ${seconds} seconde(s) avant de renvoyer un code.`;
                displayElement.classList.remove('d-none');
                seconds--;
            } else {
                clearInterval(timerId);
                displayElement.classList.add('d-none');
                btn.disabled = false;
                if (typeof onComplete === 'function') {
                    onComplete();
                }
            }
        }, 1000);

        return timerId;
    }

    /**
     * Configure la navigation automatique entre les champs OTP.
     * @param {string} groupSelector - Sélecteur CSS du groupe OTP
     */
    function initOtpNavigation(groupSelector = '#otpGroup') {
        const group = document.querySelector(groupSelector);
        if (!group) return;

        const inputs = [...group.querySelectorAll('.otp-input')];
        if (inputs.length === 0) return;

        inputs.forEach((input, index) => {
            // Forcer un seul chiffre
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9]/g, '');
                input.classList.toggle('filled', input.value !== '');
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Navigation arrière
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Coller un code complet
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const digits = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, inputs.length);
                digits.split('').forEach((d, i) => {
                    if (inputs[i]) {
                        inputs[i].value = d;
                        inputs[i].classList.add('filled');
                    }
                });
                const lastIndex = Math.min(digits.length, inputs.length - 1);
                if (digits.length && inputs[lastIndex]) {
                    inputs[lastIndex].focus();
                }
            });
        });

        return inputs;
    }

    /**
     * Récupère le code OTP complet depuis les champs individuels.
     * @param {string} groupSelector - Sélecteur CSS du groupe OTP
     * @returns {string}
     */
    function getOtpCode(groupSelector = '#otpGroup') {
        const inputs = [...document.querySelectorAll(`${groupSelector} .otp-input`)];
        return inputs.map(input => input.value).join('');
    }

    /**
     * Réinitialise l'état du bouton avec spinner.
     * @param {HTMLElement} btn
     */
    function resetButton(btn) {
        btn.disabled = false;
        const spinner = btn.querySelector('.spinner-border');
        if (spinner) {
            spinner.classList.add('d-none');
        }
    }

    /**
     * Active le spinner et désactive le bouton.
     * @param {HTMLElement} btn
     */
    function disableWithSpinner(btn) {
        btn.disabled = true;
        const spinner = btn.querySelector('.spinner-border');
        if (spinner) {
            spinner.classList.remove('d-none');
        }
    }

    // API publique
    return {
        showAlert,
        clearAlerts,
        ajaxRequest,
        startCountdown,
        initOtpNavigation,
        getOtpCode,
        resetButton,
        disableWithSpinner,
    };
})();