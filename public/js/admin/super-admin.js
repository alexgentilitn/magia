/**
 * Super Admin Dashboard - JavaScript
 * Gestione funzionalità AJAX per pannello manutenzione con Tailwind CSS
 */

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Helper per mostrare notifiche con SweetAlert2
    function showToast(message, type = 'success') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Helper per aprire modal
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    // Helper per chiudere modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Toggle Debug Mode
    const toggleDebugBtn = document.getElementById('toggleDebugBtn');
    if (toggleDebugBtn) {
        toggleDebugBtn.addEventListener('click', async function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Attendere...';

            try {
                const response = await fetch(window.SuperAdminRoutes.toggleDebug, {
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
                    showToast(data.message || 'Errore durante operazione', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante operazione', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Clear All Cache
    const clearAllCacheBtn = document.getElementById('clearAllCacheBtn');
    if (clearAllCacheBtn) {
        clearAllCacheBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: 'Conferma',
                text: 'Sei sicuro di voler pulire tutta la cache?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, pulisci',
                cancelButtonText: 'Annulla'
            });

            if (!result.isConfirmed) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Pulizia...';

            try {
                const response = await fetch(window.SuperAdminRoutes.clearAllCache, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                // Log detailed response info
                console.log('Response status:', response.status);
                console.log('Response OK:', response.ok);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    // Show detailed results if available
                    let message = data.message;
                    if (data.results) {
                        console.log('Cache results:', data.results);
                    }
                    showToast(message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    console.error('Error message:', data.message);
                    showToast(data.message || 'Errore pulizia cache', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Exception during cache clear:', error);
                showToast('Errore pulizia cache: ' + error.message, 'error');
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
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Attendere...';

            try {
                const response = await fetch(window.SuperAdminRoutes.clearConfigCache, {
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
                showToast('Errore pulizia cache config', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // View Logs
    const viewLogsBtn = document.getElementById('viewLogsBtn');
    if (viewLogsBtn) {
        viewLogsBtn.addEventListener('click', async function() {
            const logContent = document.getElementById('logContent');

            logContent.textContent = 'Caricamento...';
            openModal('logModal');

            try {
                const response = await fetch(window.SuperAdminRoutes.viewLogs, {
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
                    logContent.textContent = 'Errore caricamento log: ' + (data.message || '');
                }
            } catch (error) {
                console.error('Errore:', error);
                logContent.textContent = 'Errore caricamento log';
            }
        });
    }

    // Clear Logs
    const clearLogsBtn = document.getElementById('clearLogsBtn');
    if (clearLogsBtn) {
        clearLogsBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: 'Conferma',
                text: 'Sei sicuro di voler cancellare tutto il log?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, cancella',
                cancelButtonText: 'Annulla'
            });

            if (!result.isConfirmed) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Attendere...';

            try {
                const response = await fetch(window.SuperAdminRoutes.clearLogs, {
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
                showToast('Errore cancellazione log', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Database Info
    const databaseInfoBtn = document.getElementById('databaseInfoBtn');
    if (databaseInfoBtn) {
        databaseInfoBtn.addEventListener('click', async function() {
            const databaseContent = document.getElementById('databaseContent');

            databaseContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i><p class="mt-2 text-gray-600">Caricamento...</p></div>';
            openModal('databaseModal');

            try {
                const response = await fetch(window.SuperAdminRoutes.databaseInfo, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    let html = '<div class="mb-4"><h6 class="font-bold">Database: <span class="text-green-600">' + data.database + '</span></h6><p class="text-sm text-gray-600">Totale tabelle: <strong>' + data.total_tables + '</strong></p></div><div class="overflow-auto max-h-96"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tabella</th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Righe</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">';

                    data.tables.forEach(table => {
                        html += '<tr><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + table.name + '</td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">' + table.rows.toLocaleString() + '</td></tr>';
                    });

                    html += '</tbody></table></div>';
                    databaseContent.innerHTML = html;
                } else {
                    databaseContent.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><p class="text-red-700">Errore: ' + (data.message || '') + '</p></div>';
                }
            } catch (error) {
                console.error('Errore:', error);
                databaseContent.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><p class="text-red-700">Errore caricamento info database</p></div>';
            }
        });
    }

    // Optimize Database
    const optimizeDatabaseBtn = document.getElementById('optimizeDatabaseBtn');
    if (optimizeDatabaseBtn) {
        optimizeDatabaseBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: 'Conferma',
                text: 'Ottimizzare il database? Questa operazione potrebbe richiedere alcuni minuti.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, ottimizza',
                cancelButtonText: 'Annulla'
            });

            if (!result.isConfirmed) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Ottimizzazione...';

            try {
                const response = await fetch(window.SuperAdminRoutes.optimizeDatabase, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Database ottimizzato! ' + data.tables_optimized + ' tabelle processate', 'success');
                } else {
                    showToast(data.message || 'Errore', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore ottimizzazione database', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Run Migrations
    const runMigrationsBtn = document.getElementById('runMigrationsBtn');
    if (runMigrationsBtn) {
        runMigrationsBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: 'Conferma',
                text: 'Eseguire le migrations? Questa operazione modificherà lo schema del database.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, esegui',
                cancelButtonText: 'Annulla'
            });

            if (!result.isConfirmed) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Esecuzione...';

            try {
                const response = await fetch(window.SuperAdminRoutes.runMigrations, {
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
                    showToast(data.message || 'Errore esecuzione migrations', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore esecuzione migrations', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // ============================================
    // GESTIONE BACKUP DATABASE
    // ============================================

    // Crea Backup
    const createBackupBtn = document.getElementById('createBackupBtn');
    if (createBackupBtn) {
        createBackupBtn.addEventListener('click', async function() {
            const { value: description } = await Swal.fire({
                title: 'Crea Backup Database',
                html: '<p class="mb-2">Vuoi aggiungere una descrizione al backup?</p>',
                input: 'text',
                inputPlaceholder: 'Descrizione (opzionale)',
                showCancelButton: true,
                confirmButtonText: 'Crea Backup',
                cancelButtonText: 'Annulla',
                confirmButtonColor: '#6366f1'
            });

            if (description === undefined) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creazione backup...';

            try {
                const response = await fetch(window.SuperAdminRoutes.backupCreate, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ description })
                });

                console.log('Backup response status:', response.status);
                console.log('Backup response URL:', response.url);

                if (!response.ok) {
                    console.error('Response not OK:', response.status, response.statusText);
                }

                const data = await response.json();
                console.log('Backup response data:', data);

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Backup Creato!',
                        html: `<p>${data.message}</p>`,
                        confirmButtonColor: '#6366f1'
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore Backup',
                        html: `<p>${data.message || 'Errore durante creazione backup'}</p>`,
                        confirmButtonColor: '#ef4444'
                    });
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore completo:', error);
                console.error('Error stack:', error.stack);
                Swal.fire({
                    icon: 'error',
                    title: 'Errore Connessione',
                    html: `<p>Errore durante creazione backup</p><small>${error.message}</small>`,
                    confirmButtonColor: '#ef4444'
                });
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Visualizza Backup
    const viewBackupsBtn = document.getElementById('viewBackupsBtn');
    if (viewBackupsBtn) {
        viewBackupsBtn.addEventListener('click', async function() {
            openModal('backupModal');

            try {
                const response = await fetch(window.SuperAdminRoutes.backupList);
                const data = await response.json();

                if (data.success && data.backups.length > 0) {
                    let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
                    html += '<thead class="bg-gray-50">';
                    html += '<tr>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dimensione</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Età</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrizione</th>';
                    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>';
                    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';

                    data.backups.forEach(backup => {
                        html += '<tr>';
                        html += `<td class="px-6 py-4 text-sm font-medium text-gray-900">${backup.filename}</td>`;
                        html += `<td class="px-6 py-4 text-sm text-gray-500">${backup.size_human}</td>`;
                        html += `<td class="px-6 py-4 text-sm text-gray-500">${backup.created_at_human}</td>`;
                        html += `<td class="px-6 py-4 text-sm text-gray-500">${backup.age_days} giorni</td>`;
                        html += `<td class="px-6 py-4 text-sm text-gray-500">${backup.description || '-'}</td>`;
                        html += '<td class="px-6 py-4 text-right text-sm font-medium">';
                        html += `<button onclick="downloadBackup('${backup.filename}')" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-download"></i></button>`;
                        html += `<button onclick="deleteBackup('${backup.filename}')" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>`;
                        html += '</td></tr>';
                    });

                    html += '</tbody></table></div>';
                    document.getElementById('backupListContent').innerHTML = html;
                } else {
                    document.getElementById('backupListContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600">Nessun backup disponibile</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Errore:', error);
                document.getElementById('backupListContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-2"></i>
                        <p class="text-red-600">Errore durante caricamento backup</p>
                    </div>
                `;
            }
        });
    }

    // Pulisci Backup Vecchi
    const cleanOldBackupsBtn = document.getElementById('cleanOldBackupsBtn');
    if (cleanOldBackupsBtn) {
        cleanOldBackupsBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: 'Conferma',
                text: 'Vuoi eliminare i backup più vecchi? Verranno mantenuti solo gli ultimi 30.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, pulisci',
                cancelButtonText: 'Annulla',
                confirmButtonColor: '#f59e0b'
            });

            if (!result.isConfirmed) return;

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Pulizia...';

            try {
                const response = await fetch(window.SuperAdminRoutes.backupCleanOld, {
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
                    showToast(data.message || 'Errore durante pulizia', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            } catch (error) {
                console.error('Errore:', error);
                showToast('Errore durante pulizia', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }
});

// Funzioni globali per backup (chiamate da onclick nei bottoni)
window.downloadBackup = function(filename) {
    window.location.href = window.SuperAdminRoutes.backupDownload + '?filename=' + encodeURIComponent(filename);
};

window.deleteBackup = async function(filename) {
    const result = await Swal.fire({
        title: 'Conferma Eliminazione',
        html: `Vuoi davvero eliminare il backup:<br><strong>${filename}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, elimina',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#ef4444'
    });

    if (!result.isConfirmed) return;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(window.SuperAdminRoutes.backupDelete, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ filename })
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Eliminato!',
                text: data.message,
                confirmButtonColor: '#10b981'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Errore',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
        }
    } catch (error) {
        console.error('Errore:', error);
        Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: 'Errore durante eliminazione backup',
            confirmButtonColor: '#ef4444'
        });
    }
};
