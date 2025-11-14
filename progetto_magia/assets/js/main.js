/**
 * Progetto Magia Donna - JavaScript Principale
 *
 * Script principale del progetto
 */

// Inizializzazione quando il DOM è caricato
document.addEventListener('DOMContentLoaded', function() {
    console.log('Progetto Magia Donna - Sistema caricato');

    // Inizializza componenti
    initForms();
    initAlerts();
    initTooltips();
});

/**
 * Inizializza gestione form
 */
function initForms() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validazione base
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showAlert('Per favore, compila tutti i campi obbligatori', 'danger');
            }
        });
    });
}

/**
 * Inizializza dismissione alerts
 */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        // Auto-close dopo 5 secondi
        setTimeout(() => {
            fadeOut(alert);
        }, 5000);

        // Aggiungi bottone di chiusura se non presente
        if (!alert.querySelector('.close-btn')) {
            const closeBtn = document.createElement('span');
            closeBtn.innerHTML = '&times;';
            closeBtn.className = 'close-btn';
            closeBtn.style.cssText = 'float: right; cursor: pointer; font-size: 1.5em; line-height: 0.8;';
            closeBtn.onclick = () => fadeOut(alert);
            alert.insertBefore(closeBtn, alert.firstChild);
        }
    });
}

/**
 * Mostra un alert dinamico
 */
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span class="close-btn" style="float: right; cursor: pointer; font-size: 1.5em; line-height: 0.8;">&times;</span>
        ${message}
    `;

    // Inserisci in cima al body o in un container specifico
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alert, container.firstChild);

    // Aggiungi handler per chiusura
    alert.querySelector('.close-btn').onclick = () => fadeOut(alert);

    // Auto-close dopo 5 secondi
    setTimeout(() => fadeOut(alert), 5000);
}

/**
 * Effetto fade out per elementi
 */
function fadeOut(element) {
    element.style.transition = 'opacity 0.5s';
    element.style.opacity = '0';
    setTimeout(() => element.remove(), 500);
}

/**
 * Inizializza tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 0.9em;
                z-index: 1000;
                pointer-events: none;
            `;

            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';

            this._tooltip = tooltip;
        });

        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                delete this._tooltip;
            }
        });
    });
}

/**
 * Conferma azione
 */
function confirmAction(message = 'Sei sicuro?') {
    return confirm(message);
}

/**
 * Formatta data in formato italiano
 */
function formatDate(date) {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

/**
 * AJAX helper
 */
function ajax(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Errore AJAX:', error);
        showAlert('Errore di comunicazione con il server', 'danger');
    });
}
