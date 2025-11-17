@extends('layouts.admin')

@section('titolo', 'Super Admin - Pannello Controllo')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-shield-check text-red-600 mr-3"></i>
                Super Admin
            </h1>
            <p class="text-gray-600 mt-1">Pannello manutenzione sistema</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-medium">
                <i class="fas fa-clock mr-2"></i>Sessione attiva
            </span>
            <a href="{{ route('admin.super-admin.logout') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </div>

    <!-- Grid principale -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Info Sistema -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Informazioni Sistema
            </h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <i class="fas fa-code text-3xl text-blue-600 mr-3"></i>
                        <div>
                            <p class="text-xs text-blue-600 font-medium">PHP Version</p>
                            <p class="text-lg font-bold text-blue-900">{{ $systemInfo['php_version'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <i class="fas fa-box text-3xl text-green-600 mr-3"></i>
                        <div>
                            <p class="text-xs text-green-600 font-medium">Laravel Version</p>
                            <p class="text-lg font-bold text-green-900">{{ $systemInfo['laravel_version'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center">
                        <i class="fas fa-server text-3xl text-purple-600 mr-3"></i>
                        <div>
                            <p class="text-xs text-purple-600 font-medium">Server</p>
                            <p class="text-sm font-bold text-purple-900">{{ Str::limit($systemInfo['server_software'], 20) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                    <div class="flex items-center">
                        <i class="fas fa-cog text-3xl text-orange-600 mr-3"></i>
                        <div>
                            <p class="text-xs text-orange-600 font-medium">Environment</p>
                            <p class="text-lg font-bold text-orange-900">{{ ucfirst($systemInfo['environment']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Memory:</span>
                    <span class="font-semibold text-gray-800 ml-1">{{ $systemInfo['memory_limit'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Max Upload:</span>
                    <span class="font-semibold text-gray-800 ml-1">{{ $systemInfo['max_upload'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Max POST:</span>
                    <span class="font-semibold text-gray-800 ml-1">{{ $systemInfo['max_post'] }}</span>
                </div>
            </div>
        </div>

        <!-- Debug Mode -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 @if($systemInfo['debug_mode']) border-red-500 @else border-green-500 @endif">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bug mr-2 @if($systemInfo['debug_mode']) text-red-600 @else text-green-600 @endif"></i>
                Debug Mode
            </h2>

            <div class="text-center py-4">
                <div class="text-6xl mb-4">
                    @if($systemInfo['debug_mode'])
                        <i class="fas fa-toggle-on text-red-600"></i>
                    @else
                        <i class="fas fa-toggle-off text-gray-400"></i>
                    @endif
                </div>

                <span class="inline-block px-4 py-2 rounded-full text-sm font-bold @if($systemInfo['debug_mode']) bg-red-100 text-red-800 @else bg-green-100 text-green-800 @endif">
                    {{ $systemInfo['debug_mode'] ? 'ABILITATO' : 'DISABILITATO' }}
                </span>

                @if($systemInfo['debug_mode'])
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 text-left">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Disabilita in produzione!
                        </p>
                    </div>
                @endif

                <button
                    id="toggleDebugBtn"
                    class="mt-6 w-full @if($systemInfo['debug_mode']) bg-red-600 hover:bg-red-700 @else bg-green-600 hover:bg-green-700 @endif text-white font-semibold py-3 px-4 rounded-lg transition transform hover:scale-105"
                >
                    <i class="fas fa-sync-alt mr-2"></i>
                    {{ $systemInfo['debug_mode'] ? 'Disabilita' : 'Abilita' }} Debug
                </button>
            </div>
        </div>
    </div>

    <!-- Gestione Cache e Log -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Cache -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-database text-blue-600 mr-2"></i>
                Gestione Cache
            </h2>

            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">File Bootstrap Cache:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($cacheInfo['files'] as $file => $exists)
                        <span class="px-3 py-1 rounded-full text-xs font-medium @if($exists) bg-yellow-100 text-yellow-800 @else bg-gray-100 text-gray-600 @endif">
                            {{ $file }}: {{ $exists ? 'Presente' : 'Assente' }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Views in cache: <strong>{{ $cacheInfo['views_cached'] }}</strong> file</p>
            </div>

            <div class="space-y-2">
                <button id="clearAllCacheBtn" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                    <i class="fas fa-trash mr-2"></i> Pulisci Tutta la Cache
                </button>
                <button id="clearConfigCacheBtn" class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-semibold py-3 px-4 rounded-lg transition">
                    <i class="fas fa-cog mr-2"></i> Pulisci Solo Cache Config
                </button>
            </div>
        </div>

        <!-- Log -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-alt text-orange-600 mr-2"></i>
                Gestione Log
            </h2>

            @if($logInfo['exists'])
                <div class="mb-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Dimensione:</span>
                        <span class="font-semibold text-gray-800">{{ $logInfo['size_human'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Ultimo aggiornamento:</span>
                        <span class="text-xs text-gray-600">{{ $logInfo['modified_human'] }}</span>
                    </div>
                </div>

                @if($logInfo['size'] > 1048576)
                    <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-3">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Log molto grande, considera di pulirlo
                        </p>
                    </div>
                @endif

                <div class="space-y-2">
                    <button id="viewLogsBtn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <i class="fas fa-eye mr-2"></i> Visualizza Log
                    </button>
                    <button id="clearLogsBtn" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <i class="fas fa-trash mr-2"></i> Pulisci Log
                    </button>
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Nessun file di log presente
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Backup Database -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-save text-indigo-600 mr-2"></i>
                Backup Database
            </h2>
            @if(!$mysqldumpAvailable)
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                    <i class="fas fa-exclamation-triangle mr-1"></i>mysqldump non disponibile
                </span>
            @else
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Sistema pronto
                </span>
            @endif
        </div>

        <!-- Statistiche Backup -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-lg border border-indigo-200">
                <div class="flex items-center">
                    <i class="fas fa-archive text-2xl text-indigo-600 mr-3"></i>
                    <div>
                        <p class="text-xs text-indigo-600 font-medium">Backup Totali</p>
                        <p class="text-lg font-bold text-indigo-900">{{ $backupStats['total_backups'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <i class="fas fa-hdd text-2xl text-purple-600 mr-3"></i>
                    <div>
                        <p class="text-xs text-purple-600 font-medium">Spazio Utilizzato</p>
                        <p class="text-lg font-bold text-purple-900">{{ $backupStats['total_size_human'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <i class="fas fa-clock text-2xl text-blue-600 mr-3"></i>
                    <div>
                        <p class="text-xs text-blue-600 font-medium">Ultimo Backup</p>
                        <p class="text-sm font-bold text-blue-900">{{ $backupStats['newest_backup'] ?? 'Mai' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Azioni Backup -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <button id="createBackupBtn"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg transition {{ !$mysqldumpAvailable ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ !$mysqldumpAvailable ? 'disabled' : '' }}>
                <i class="fas fa-plus-circle mr-2"></i> Crea Backup Ora
            </button>
            <button id="viewBackupsBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                <i class="fas fa-list mr-2"></i> Visualizza Backup ({{ $backupStats['total_backups'] }})
            </button>
            <button id="cleanOldBackupsBtn" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                <i class="fas fa-broom mr-2"></i> Pulisci Backup Vecchi
            </button>
        </div>

        @if(!$mysqldumpAvailable)
        <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
            <p class="text-sm text-red-700">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Attenzione:</strong> mysqldump non è disponibile su questo server. Contatta l'amministratore di sistema per installarlo.
            </p>
        </div>
        @endif
    </div>

    <!-- Strumenti Database -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-database text-green-600 mr-2"></i>
            Strumenti Database
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <button id="databaseInfoBtn" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-3 px-4 rounded-lg transition">
                <i class="fas fa-info-circle mr-2"></i> Info Database
            </button>
            <button id="optimizeDatabaseBtn" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-3 px-4 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i> Ottimizza Database
            </button>
            <button id="runMigrationsBtn" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-3 px-4 rounded-lg transition">
                <i class="fas fa-sync-alt mr-2"></i> Esegui Migrations
            </button>
            <a href="{{ route('admin.impostazioni-sistema.index') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-3 px-4 rounded-lg transition text-center">
                <i class="fas fa-cog mr-2"></i> Impostazioni Sistema
            </a>
        </div>
    </div>
</div>

<!-- Modal Log -->
<div id="logModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i> Laravel Log
            </h3>
            <button onclick="document.getElementById('logModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="bg-gray-900 p-4 rounded-lg overflow-auto max-h-96">
            <pre id="logContent" class="text-gray-100 text-xs font-mono"></pre>
        </div>
    </div>
</div>

<!-- Modal Database -->
<div id="databaseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-database mr-2"></i> Informazioni Database
            </h3>
            <button onclick="document.getElementById('databaseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="databaseContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                <p class="mt-2 text-gray-600">Caricamento...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Backup List -->
<div id="backupModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-archive mr-2"></i> Lista Backup Database
            </h3>
            <button onclick="document.getElementById('backupModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="backupListContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                <p class="mt-2 text-gray-600">Caricamento backup...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Migrations Manager -->
<div id="migrationsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-code-branch mr-2"></i> Gestione Migrations Database
            </h3>
            <button onclick="document.getElementById('migrationsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Stats e Azioni -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600" id="migrationsTotalCount">-</div>
                <div class="text-sm text-gray-600">Totale Migrations</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600" id="migrationsRanCount">-</div>
                <div class="text-sm text-gray-600">Eseguite</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600" id="migrationsPendingCount">-</div>
                <div class="text-sm text-gray-600">In Attesa</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg flex items-center justify-center">
                <button id="refreshMigrationsBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i> Aggiorna
                </button>
            </div>
        </div>

        <!-- Azioni di Massa -->
        <div class="mb-4 flex gap-2 flex-wrap">
            <button id="runSelectedMigrationsBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-play mr-2"></i> Esegui Selezionate
            </button>
            <button id="runAllPendingBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-forward mr-2"></i> Esegui Tutte in Attesa
            </button>
            <button id="selectAllPendingBtn" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-check-double mr-2"></i> Seleziona Tutte in Attesa
            </button>
            <button id="deselectAllBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-times mr-2"></i> Deseleziona Tutto
            </button>
        </div>

        <!-- Lista Migrations -->
        <div id="migrationsListContent" class="max-h-96 overflow-y-auto">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                <p class="mt-2 text-gray-600">Caricamento migrations...</p>
            </div>
        </div>
    </div>
</div>

<!-- Definizione rotte per JavaScript -->
<script>
    window.SuperAdminRoutes = {
        toggleDebug: "{{ route('admin.super-admin.toggle-debug') }}",
        clearAllCache: "{{ route('admin.super-admin.clear-all-cache') }}",
        clearConfigCache: "{{ route('admin.super-admin.clear-config-cache') }}",
        viewLogs: "{{ route('admin.super-admin.view-logs') }}",
        clearLogs: "{{ route('admin.super-admin.clear-logs') }}",
        databaseInfo: "{{ route('admin.super-admin.database-info') }}",
        optimizeDatabase: "{{ route('admin.super-admin.optimize-database') }}",
        migrationsStatus: "{{ route('admin.super-admin.migrations.status') }}",
        runMigrations: "{{ route('admin.super-admin.run-migrations') }}",
        migrationsRollback: "{{ route('admin.super-admin.migrations.rollback') }}",
        migrationsDelete: "{{ route('admin.super-admin.migrations.delete') }}",
        backupCreate: "{{ route('admin.super-admin.backup.create') }}",
        backupList: "{{ route('admin.super-admin.backup.list') }}",
        backupDownload: "{{ route('admin.super-admin.backup.download') }}",
        backupDelete: "{{ route('admin.super-admin.backup.delete') }}",
        backupCleanOld: "{{ route('admin.super-admin.backup.clean-old') }}"
    };
</script>
<script src="{{ asset('js/admin/super-admin.js') }}"></script>
@endsection
