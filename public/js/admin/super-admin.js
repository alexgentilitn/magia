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

    // Open Migrations Manager
    const runMigrationsBtn = document.getElementById('runMigrationsBtn');
    if (runMigrationsBtn) {
        runMigrationsBtn.addEventListener('click', function() {
            openMigrationsManager();
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

// ============================================
// GESTIONE MIGRATIONS
// ============================================

/**
 * Apre il modal di gestione migrations
 */
function openMigrationsManager() {
    document.getElementById('migrationsModal').classList.remove('hidden');
    loadMigrationsList();
}

/**
 * Carica la lista di tutte le migrations con il loro stato
 */
async function loadMigrationsList() {
    const container = document.getElementById('migrationsListContent');
    container.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-2 text-gray-600">Caricamento migrations...</p>
        </div>
    `;

    try {
        const response = await fetch(window.SuperAdminRoutes.migrationsStatus, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            displayMigrations(data.migrations, data);
        } else {
            container.innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-circle text-4xl"></i>
                    <p class="mt-2">${data.message || 'Errore caricamento migrations'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Errore:', error);
        container.innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-circle text-4xl"></i>
                <p class="mt-2">Errore caricamento migrations</p>
            </div>
        `;
    }
}

/**
 * Visualizza la lista delle migrations
 */
function displayMigrations(migrations, stats) {
    // Aggiorna statistiche
    document.getElementById('migrationsTotalCount').textContent = stats.total;
    document.getElementById('migrationsRanCount').textContent = stats.ran;
    document.getElementById('migrationsPendingCount').textContent = stats.pending;

    const container = document.getElementById('migrationsListContent');

    if (migrations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl"></i>
                <p class="mt-2">Nessuna migration trovata</p>
            </div>
        `;
        return;
    }

    let html = '<div class="space-y-2">';

    migrations.forEach(migration => {
        const isPending = migration.status === 'pending';
        const statusColor = isPending ? 'yellow' : 'green';
        const statusIcon = isPending ? 'clock' : 'check-circle';
        const statusText = isPending ? 'In Attesa' : 'Eseguita';

        html += `
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition ${isPending ? 'bg-yellow-50 border-yellow-200' : 'bg-white border-gray-200'}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        ${isPending ? `
                            <input type="checkbox" class="migration-checkbox w-5 h-5 text-blue-600"
                                   data-migration="${migration.name}" data-status="${migration.status}">
                        ` : ''}
                        <div class="flex-1">
                            <div class="font-mono text-sm text-gray-900">${migration.name}</div>
                            ${migration.batch ? `<div class="text-xs text-gray-500 mt-1">Batch: ${migration.batch}</div>` : ''}
                            ${migration.executed_at ? `<div class="text-xs text-gray-500">Eseguita: ${migration.executed_at}</div>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">
                            <i class="fas fa-${statusIcon} mr-1"></i> ${statusText}
                        </span>
                        ${isPending ? `
                            <button onclick="runSingleMigration('${migration.name}')"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition">
                                <i class="fas fa-play mr-1"></i> Esegui
                            </button>
                            <button onclick="deleteMigration('${migration.name}')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition">
                                <i class="fas fa-trash mr-1"></i> Elimina
                            </button>
                        ` : `
                            <button onclick="rollbackMigration('${migration.name}')"
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs transition">
                                <i class="fas fa-undo mr-1"></i> Rollback
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;

    // Setup event listeners per i pulsanti di massa
    setupMigrationsButtons();
}

/**
 * Setup event listeners per i pulsanti
 */
function setupMigrationsButtons() {
    // Refresh
    document.getElementById('refreshMigrationsBtn').addEventListener('click', loadMigrationsList);

    // Run Selected
    document.getElementById('runSelectedMigrationsBtn').addEventListener('click', runSelectedMigrations);

    // Run All Pending
    document.getElementById('runAllPendingBtn').addEventListener('click', runAllPendingMigrations);

    // Select All Pending
    document.getElementById('selectAllPendingBtn').addEventListener('click', function() {
        document.querySelectorAll('.migration-checkbox[data-status="pending"]').forEach(cb => cb.checked = true);
    });

    // Deselect All
    document.getElementById('deselectAllBtn').addEventListener('click', function() {
        document.querySelectorAll('.migration-checkbox').forEach(cb => cb.checked = false);
    });
}

/**
 * Esegue una singola migration
 */
async function runSingleMigration(migrationName) {
    const result = await Swal.fire({
        title: 'Conferma Esecuzione',
        html: `Eseguire la migration:<br><code class="text-sm">${migrationName}</code>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sì, esegui',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#10b981'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(window.SuperAdminRoutes.runMigrations, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                migrations: [migrationName]
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Eseguita!',
                text: data.message,
                confirmButtonColor: '#10b981'
            });
            loadMigrationsList();
        } else {
            // Crea messaggio dettagliato con risultati
            let htmlMessage = '<div class="text-left">';
            htmlMessage += '<p class="mb-2">' + data.message + '</p>';

            if (data.results) {
                htmlMessage += '<div class="mt-4 max-h-64 overflow-y-auto text-sm">';
                for (const [migration, result] of Object.entries(data.results)) {
                    if (!result.success) {
                        htmlMessage += '<div class="bg-red-50 border-l-2 border-red-400 p-2 mb-2">';
                        htmlMessage += '<strong>' + migration + '</strong><br>';
                        htmlMessage += '<span class="text-red-700">' + (result.error || 'Errore sconosciuto') + '</span>';
                        if (result.output) {
                            htmlMessage += '<pre class="mt-1 text-xs bg-gray-100 p-1 rounded overflow-x-auto">' + result.output + '</pre>';
                        }
                        htmlMessage += '</div>';
                    }
                }
                htmlMessage += '</div>';
            }

            if (data.debug) {
                htmlMessage += '<div class="mt-4 text-xs bg-yellow-50 p-2 rounded">';
                htmlMessage += '<strong>Debug Info:</strong><br>';
                htmlMessage += 'Host: ' + data.debug.host + '<br>';
                htmlMessage += 'Database: ' + data.debug.database + '<br>';
                htmlMessage += 'Error: ' + data.debug.error_type;
                htmlMessage += '</div>';
            }

            htmlMessage += '</div>';

            Swal.fire({
                icon: 'error',
                title: 'Errore Esecuzione',
                html: htmlMessage,
                width: '600px',
                confirmButtonColor: '#ef4444'
            });
        }
    } catch (error) {
        console.error('Errore:', error);
        Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: 'Errore durante esecuzione migration',
            confirmButtonColor: '#ef4444'
        });
    }
}

/**
 * Esegue le migrations selezionate
 */
async function runSelectedMigrations() {
    const selectedCheckboxes = document.querySelectorAll('.migration-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Attenzione',
            text: 'Seleziona almeno una migration',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    const migrations = Array.from(selectedCheckboxes).map(cb => cb.dataset.migration);

    const result = await Swal.fire({
        title: 'Conferma Esecuzione',
        html: `Eseguire <strong>${migrations.length}</strong> migration(s) selezionate?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sì, esegui',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#10b981'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(window.SuperAdminRoutes.runMigrations, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                migrations: migrations
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Eseguite!',
                text: data.message,
                confirmButtonColor: '#10b981'
            });
            loadMigrationsList();
        } else {
            // Crea messaggio dettagliato con risultati
            let htmlMessage = '<div class="text-left">';
            htmlMessage += '<p class="mb-2">' + data.message + '</p>';

            if (data.results) {
                htmlMessage += '<div class="mt-4 max-h-64 overflow-y-auto text-sm">';
                for (const [migration, result] of Object.entries(data.results)) {
                    if (!result.success) {
                        htmlMessage += '<div class="bg-red-50 border-l-2 border-red-400 p-2 mb-2">';
                        htmlMessage += '<strong>' + migration + '</strong><br>';
                        htmlMessage += '<span class="text-red-700">' + (result.error || 'Errore sconosciuto') + '</span>';
                        if (result.output) {
                            htmlMessage += '<pre class="mt-1 text-xs bg-gray-100 p-1 rounded overflow-x-auto">' + result.output + '</pre>';
                        }
                        htmlMessage += '</div>';
                    }
                }
                htmlMessage += '</div>';
            }

            if (data.debug) {
                htmlMessage += '<div class="mt-4 text-xs bg-yellow-50 p-2 rounded">';
                htmlMessage += '<strong>Debug Info:</strong><br>';
                htmlMessage += 'Host: ' + data.debug.host + '<br>';
                htmlMessage += 'Database: ' + data.debug.database + '<br>';
                htmlMessage += 'Error: ' + data.debug.error_type;
                htmlMessage += '</div>';
            }

            htmlMessage += '</div>';

            Swal.fire({
                icon: 'error',
                title: 'Errore Esecuzione',
                html: htmlMessage,
                width: '600px',
                confirmButtonColor: '#ef4444'
            });
        }
    } catch (error) {
        console.error('Errore:', error);
        Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: 'Errore durante esecuzione migrations',
            confirmButtonColor: '#ef4444'
        });
    }
}

/**
 * Esegue tutte le migrations in attesa
 */
async function runAllPendingMigrations() {
    const result = await Swal.fire({
        title: 'Conferma Esecuzione',
        text: 'Eseguire TUTTE le migrations in attesa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, esegui tutte',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#10b981'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(window.SuperAdminRoutes.runMigrations, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({}) // Empty per eseguire tutte
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Eseguite!',
                text: data.message,
                confirmButtonColor: '#10b981'
            });
            loadMigrationsList();
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
            text: 'Errore durante esecuzione migrations',
            confirmButtonColor: '#ef4444'
        });
    }
}

/**
 * Rollback di una migration
 */
async function rollbackMigration(migrationName) {
    const result = await Swal.fire({
        title: 'Conferma Rollback',
        html: `<p class="mb-2">Eseguire il rollback della migration:</p><code class="text-sm">${migrationName}</code><br><br><strong class="text-red-600">ATTENZIONE: Questa operazione eliminerà le modifiche al database!</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, rollback',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#f97316'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(window.SuperAdminRoutes.migrationsRollback, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                migration: migrationName
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Rollback Eseguito!',
                text: data.message,
                confirmButtonColor: '#10b981'
            });
            loadMigrationsList();
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
            text: 'Errore durante rollback migration',
            confirmButtonColor: '#ef4444'
        });
    }
}

/**
 * Elimina file migration
 */
async function deleteMigration(migrationName) {
    const result = await Swal.fire({
        title: 'Conferma Eliminazione',
        html: `<p class="mb-2">Eliminare il file migration:</p><code class="text-sm">${migrationName}.php</code><br><br><strong class="text-red-600">ATTENZIONE: Questa operazione è irreversibile!</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, elimina',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#ef4444'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(window.SuperAdminRoutes.migrationsDelete, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                migration: migrationName
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Eliminata!',
                text: data.message,
                confirmButtonColor: '#10b981'
            });
            loadMigrationsList();
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
            text: 'Errore durante eliminazione migration',
            confirmButtonColor: '#ef4444'
        });
    }
}
