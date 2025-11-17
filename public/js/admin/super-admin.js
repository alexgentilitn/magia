/**
 * Super Admin Dashboard - JavaScript
 * Gestione funzionalità AJAX per pannello manutenzione
 */

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Helper per mostrare toast/notifiche
    function showToast(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.style.minWidth = '300px';
        alert.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // Toggle Debug Mode
    const toggleDebugBtn = document.getElementById('toggleDebugBtn');
    if (toggleDebugBtn) {
        toggleDebugBtn.addEventListener('click', async function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Attendere...';

            try {
                const response = await fetch('/admin/super-admin/toggle-debug', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Errore durante l\'operazione', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante l\'operazione', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Clear All Cache
    const clearAllCacheBtn = document.getElementById('clearAllCacheBtn');
    if (clearAllCacheBtn) {
        clearAllCacheBtn.addEventListener('click', async function() {
            if (!confirm('Sei sicuro di voler pulire tutta la cache?')) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Pulizia in corso...';

            try {
                const response = await fetch('/admin/super-admin/clear-all-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Errore durante la pulizia cache', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante la pulizia cache', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Clear Config Cache
    const clearConfigCacheBtn = document.getElementById('clearConfigCacheBtn');
    if (clearConfigCacheBtn) {
        clearConfigCacheBtn.addEventListener('click', async function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Attendere...';

            try {
                const response = await fetch('/admin/super-admin/clear-config-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Errore', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante la pulizia cache config', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // View Logs
    const viewLogsBtn = document.getElementById('viewLogsBtn');
    if (viewLogsBtn) {
        viewLogsBtn.addEventListener('click', async function() {
            const modal = new bootstrap.Modal(document.getElementById('logModal'));
            const logContent = document.getElementById('logContent');

            logContent.textContent = 'Caricamento...';
            modal.show();

            try {
                const response = await fetch('/admin/super-admin/view-logs', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    logContent.textContent = data.content || 'Log vuoto';
                } else {
                    logContent.textContent = 'Errore nel caricamento del log: ' + (data.message || '');
                }
            } catch (error) {
                console.error('Errore:', error);
                logContent.textContent = 'Errore nel caricamento del log';
            }
        });
    }

    // Clear Logs
    const clearLogsBtn = document.getElementById('clearLogsBtn');
    if (clearLogsBtn) {
        clearLogsBtn.addEventListener('click', async function() {
            if (!confirm('Sei sicuro di voler cancellare tutto il log?')) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Attendere...';

            try {
                const response = await fetch('/admin/super-admin/clear-logs', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Errore', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante la cancellazione log', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Database Info
    const databaseInfoBtn = document.getElementById('databaseInfoBtn');
    if (databaseInfoBtn) {
        databaseInfoBtn.addEventListener('click', async function() {
            const modal = new bootstrap.Modal(document.getElementById('databaseModal'));
            const databaseContent = document.getElementById('databaseContent');

            databaseContent.innerHTML = '<div class="text-center"><div class="spinner-border"></div><p class="mt-2">Caricamento...</p></div>';
            modal.show();

            try {
                const response = await fetch('/admin/super-admin/database-info', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    let html = `
                        <h6>Database: <strong>${data.database}</strong></h6>
                        <p>Totale tabelle: <strong>${data.total_tables}</strong></p>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Tabella</th>
                                        <th class="text-end">Righe</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.tables.forEach(table => {
                        html += `
                            <tr>
                                <td>${table.name}</td>
                                <td class="text-end">${table.rows.toLocaleString()}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                    databaseContent.innerHTML = html;
                } else {
                    databaseContent.innerHTML = '<div class="alert alert-danger">Errore: ' + (data.message || '') + '</div>';
                }
            } catch (error) {
                console.error('Errore:', error);
                databaseContent.innerHTML = '<div class="alert alert-danger">Errore nel caricamento info database</div>';
            }
        });
    }

    // Optimize Database
    const optimizeDatabaseBtn = document.getElementById('optimizeDatabaseBtn');
    if (optimizeDatabaseBtn) {
        optimizeDatabaseBtn.addEventListener('click', async function() {
            if (!confirm('Ottimizzare il database? Questa operazione potrebbe richiedere alcuni minuti.')) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ottimizzazione...';

            try {
                const response = await fetch('/admin/super-admin/optimize-database', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(`Database ottimizzato! ${data.tables_optimized} tabelle processate`, 'success');
                } else {
                    showToast(data.message || 'Errore', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante l\'ottimizzazione database', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Run Migrations
    const runMigrationsBtn = document.getElementById('runMigrationsBtn');
    if (runMigrationsBtn) {
        runMigrationsBtn.addEventListener('click', async function() {
            if (!confirm('Eseguire le migrations? Questa operazione modificherà lo schema del database.')) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Esecuzione...';

            try {
                const response = await fetch('/admin/super-admin/run-migrations', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    if (data.output) {
                        console.log('Output migrations:', data.output);
                    }
                } else {
                    showToast(data.message || 'Errore durante l\'esecuzione migrations', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante l\'esecuzione migrations', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }
});
