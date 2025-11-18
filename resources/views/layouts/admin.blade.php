<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titolo', 'Admin') - MA.GIA DONNA</title>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 per notifiche belle -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js per grafici professionali -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100">

    <!-- 🎨 NOTIFICHE CON SWEETALERT2 -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Successo!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Errore!',
                html: '{{ session('error') }}',
                confirmButtonText: 'OK',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            });
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Attenzione!',
                text: '{{ session('warning') }}',
                confirmButtonText: 'OK',
                timer: 7000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Informazione',
                text: '{{ session('info') }}',
                confirmButtonText: 'OK',
                timer: 5000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    <!-- 🔍 PANNELLO DEBUG (solo per Super Admin) -->
    @if(auth()->check() && auth()->user()->hasRole('super_admin') && config('app.debug'))
    <div id="debugPanel" style="display: none;" class="fixed bottom-0 right-0 w-96 bg-gray-900 text-green-400 shadow-2xl z-50 max-h-96 overflow-y-auto font-mono text-xs">
        <div class="bg-red-600 text-white p-2 flex justify-between items-center cursor-pointer" onclick="document.getElementById('debugPanel').style.display='none'">
            <span class="font-bold">🔍 DEBUG PANEL</span>
            <button class="text-white">✖</button>
        </div>
        <div class="p-3">
            <div class="mb-2">
                <strong class="text-yellow-400">Environment:</strong> {{ config('app.env') }}
            </div>
            <div class="mb-2">
                <strong class="text-yellow-400">Laravel:</strong> {{ app()->version() }}
            </div>
            <div class="mb-2">
                <strong class="text-yellow-400">PHP:</strong> {{ PHP_VERSION }}
            </div>
            <div class="mb-2">
                <strong class="text-yellow-400">Memory:</strong> {{ round(memory_get_usage() / 1024 / 1024, 2) }} MB
            </div>
            @if(session()->has('_debug_queries'))
            <div class="mt-3 pt-3 border-t border-gray-700">
                <strong class="text-yellow-400">Last Queries:</strong>
                <div class="mt-1 text-gray-300 text-xs">
                    @foreach(session('_debug_queries', []) as $query)
                    <div class="mb-1 p-1 bg-gray-800 rounded">{{ $query }}</div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Bottone per aprire il debug panel -->
    <button onclick="document.getElementById('debugPanel').style.display='block'" 
            class="fixed bottom-4 right-4 bg-red-600 text-white px-3 py-2 rounded-full shadow-lg hover:bg-red-700 z-40">
        🐛 DEBUG
    </button>
    @endif
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-viola-magia to-fucsia-magia text-white flex-shrink-0 hidden md:block">
            <div class="flex flex-col h-full">
                
                <div class="p-6 border-b border-white border-opacity-20">
                    <a href="{{ route('admin.dashboard') }}">
                        <h1 class="text-2xl font-bold">
                            <span class="text-white">MA.GIA</span>
                            <span class="text-pink-200">DONNA</span>
                        </h1>
                    </a>
                    <p class="text-pink-100 text-xs mt-2">Pannello Amministratore</p>
                </div>

                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="space-y-1 px-3">

                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.dashboard')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </a>
                        </li>

                        <!-- Calendario -->
                        <li>
                            <a href="{{ route('admin.calendario.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.calendario.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-calendar-alt w-5"></i>
                                <span class="ml-3 font-medium">Calendario</span>
                            </a>
                        </li>

                        <!-- Report -->
                        <li>
                            <a href="{{ route('admin.report.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.report.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3 font-medium">Report</span>
                            </a>
                        </li>

                        <!-- Chat -->
                        <li>
                            <a href="{{ route('messaging.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('messaging.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-comments w-5"></i>
                                <span class="ml-3 font-medium">Chat</span>
                                @php
                                    try {
                                        $messaggiNonLetti = auth()->user()->conversazioni()->get()->sum(function($conv) {
                                            return $conv->utenti()->where('utente_id', auth()->id())->first()->pivot->messaggi_non_letti ?? 0;
                                        });
                                    } catch (\Exception $e) {
                                        $messaggiNonLetti = 0;
                                    }
                                @endphp
                                @if($messaggiNonLetti > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        {{ $messaggiNonLetti > 9 ? '9+' : $messaggiNonLetti }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <!-- Notifiche -->
                        <li>
                            <a href="{{ route('notifiche.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('notifiche.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-bell w-5"></i>
                                <span class="ml-3 font-medium">Notifiche</span>
                                @php
                                    try {
                                        $notificheNonLette = \App\Models\Notifica::where('utente_id', auth()->id())->where('letta', false)->count();
                                    } catch (\Exception $e) {
                                        $notificheNonLette = 0;
                                    }
                                @endphp
                                @if($notificheNonLette > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        {{ $notificheNonLette > 9 ? '9+' : $notificheNonLette }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="px-4 py-2">
                            <div class="border-t border-white border-opacity-20"></div>
                            <p class="text-xs text-pink-200 mt-2 font-semibold uppercase">Gestione</p>
                        </li>

                        <!-- Clienti -->
                        <li>
                            <a href="{{ route('admin.clienti.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.clienti.index')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3 font-medium">Clienti</span>
                            </a>
                        </li>

                        <!-- Clienti con Pesate -->
                        <li>
                            <a href="{{ route('admin.clienti-pesate.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.clienti-pesate.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-weight w-5"></i>
                                <span class="ml-3 font-medium">Clienti con Pesate</span>
                            </a>
                        </li>

                        <!-- Importa Pesate Excel -->
                        <li>
                            <a href="{{ route('admin.pesate.import') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.pesate.import') || request()->routeIs('admin.pesate.process-import') || request()->routeIs('admin.pesate.confirm-import')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-file-excel w-5"></i>
                                <span class="ml-3 font-medium">Importa Pesate</span>
                            </a>
                        </li>

                        <!-- Lezioni -->
                        <li>
                            <a href="{{ route('admin.lezioni.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.lezioni.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-calendar-alt w-5"></i>
                                <span class="ml-3 font-medium">Lezioni</span>
                            </a>
                        </li>

                        <!-- Programmi -->
                        <li>
                            <a href="{{ route('admin.programmi.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.programmi.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-dumbbell w-5"></i>
                                <span class="ml-3 font-medium">Programmi</span>
                            </a>
                        </li>

                        <!-- Pagamenti -->
                        <li>
                            <a href="{{ route('admin.pagamenti.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.pagamenti.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-euro-sign w-5"></i>
                                <span class="ml-3 font-medium">Pagamenti</span>
                            </a>
                        </li>

                        <!-- Sedi -->
                        <li>
                            <a href="{{ route('admin.sedi.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.sedi.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span class="ml-3 font-medium">Sedi</span>
                            </a>
                        </li>

                        <!-- Professionisti -->
                        <li>
                            <a href="{{ route('admin.professionisti.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.professionisti.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-user-tie w-5"></i>
                                <span class="ml-3 font-medium">Professionisti</span>
                            </a>
                        </li>

                        <!-- Manutenzione (solo amministratori) -->
                        @if(Auth::user()->tipo_utente === 'amministratore')
                        <li>
                            <a href="{{ route('admin.maintenance.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.maintenance.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-tools w-5"></i>
                                <span class="ml-3 font-medium">Manutenzione</span>
                            </a>
                        </li>

                        <!-- Impostazioni (solo amministratori) -->
                        <li>
                            <a href="{{ route('admin.impostazioni.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.impostazioni.*') || request()->routeIs('admin.impostazioni-sistema.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-cog w-5"></i>
                                <span class="ml-3 font-medium">Impostazioni</span>
                            </a>
                        </li>

                        <!-- Super Admin (solo amministratori) -->
                        <li class="mt-2 pt-2 border-t border-white border-opacity-20">
                            <a href="{{ route('admin.super-admin.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-red-600 hover:bg-opacity-30 rounded-lg transition @if(request()->routeIs('admin.super-admin.*')) bg-red-600 bg-opacity-30 @endif">
                                <i class="fas fa-shield-alt w-5"></i>
                                <span class="ml-3 font-medium">Super Admin</span>
                                <span class="ml-auto text-xs bg-red-600 px-2 py-1 rounded">PRO</span>
                            </a>
                        </li>
                        @endif

                    </ul>
                </nav>

                <div class="p-4 border-t border-white border-opacity-20">
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-viola-magia font-bold">
                            {{ substr(auth()->user()->nome, 0, 1) }}{{ substr(auth()->user()->cognome, 0, 1) }}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ auth()->user()->nome }}</p>
                            <p class="text-xs text-pink-200">{{ ucfirst(auth()->user()->tipo_utente) }}</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.profilo.index') }}" class="w-full flex items-center justify-center px-4 py-2 mb-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition text-white">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span class="font-medium">Profilo</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition text-white">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 py-4 flex items-center justify-between">
                    
                    <button class="md:hidden text-gray-600" x-data @click="$dispatch('toggle-mobile-menu')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="md:hidden">
                        <h1 class="text-xl font-bold">
                            <span class="text-viola-magia">MA.GIA</span>
                            <span class="text-fucsia-magia">DONNA</span>
                        </h1>
                    </div>

                    <div class="hidden md:block">
                        <h1 class="text-xl font-bold text-gray-800">@yield('titolo', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="relative text-gray-600 hover:text-fucsia-magia">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>

                        <div class="md:hidden h-8 w-8 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->nome, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                @yield('contenuto')
            </main>

        </div>

    </div>

    <!-- Mobile Sidebar -->
    <div x-data="{ open: false }" 
         @toggle-mobile-menu.window="open = !open"
         x-show="open"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden">
        
        <div class="w-64 h-full bg-gradient-to-b from-viola-magia to-fucsia-magia text-white flex flex-col">
            <div class="p-6 border-b border-white border-opacity-20 flex items-center justify-between">
                <h1 class="text-xl font-bold">
                    <span class="text-white">MA.GIA</span>
                    <span class="text-pink-200">DONNA</span>
                </h1>
                <button @click="open = false" class="text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex-1 py-4">
                <ul class="space-y-1 px-3">

                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                    <!-- Calendario -->
                    <li>
                        <a href="{{ route('admin.calendario.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-calendar-alt w-5"></i>
                            <span class="ml-3">Calendario</span>
                        </a>
                    </li>

                    <!-- Report -->
                    <li>
                        <a href="{{ route('admin.report.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="ml-3">Report</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="px-4 py-2">
                        <div class="border-t border-white border-opacity-20"></div>
                        <p class="text-xs text-pink-200 mt-2 font-semibold uppercase">Gestione</p>
                    </li>

                    <!-- Clienti -->
                    <li>
                        <a href="{{ route('admin.clienti.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-users w-5"></i>
                            <span class="ml-3">Clienti</span>
                        </a>
                    </li>

                    <!-- Lezioni -->
                    <li>
                        <a href="{{ route('admin.lezioni.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-calendar-alt w-5"></i>
                            <span class="ml-3">Lezioni</span>
                        </a>
                    </li>

                    <!-- Programmi -->
                    <li>
                        <a href="{{ route('admin.programmi.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-dumbbell w-5"></i>
                            <span class="ml-3">Programmi</span>
                        </a>
                    </li>

                    <!-- Pagamenti -->
                    <li>
                        <a href="{{ route('admin.pagamenti.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-euro-sign w-5"></i>
                            <span class="ml-3">Pagamenti</span>
                        </a>
                    </li>

                    <!-- Sedi -->
                    <li>
                        <a href="{{ route('admin.sedi.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-map-marker-alt w-5"></i>
                            <span class="ml-3">Sedi</span>
                        </a>
                    </li>

                    <!-- Professionisti -->
                    <li>
                        <a href="{{ route('admin.professionisti.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-user-tie w-5"></i>
                            <span class="ml-3">Professionisti</span>
                        </a>
                    </li>

                    <!-- Impostazioni (solo amministratori) -->
                    @if(Auth::user()->tipo_utente === 'amministratore')
                    <li>
                        <a href="{{ route('admin.impostazioni.smtp') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-cog w-5"></i>
                            <span class="ml-3">Impostazioni</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </nav>

            <div class="p-4 border-t border-white border-opacity-20">
                <a href="{{ route('admin.profilo.index') }}" class="w-full flex items-center justify-center px-4 py-2 mb-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition text-white">
                    <i class="fas fa-user-circle mr-2"></i>
                    <span class="font-medium">Profilo</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Globali per Conferme SweetAlert -->
    <script>
        /**
         * Conferma eliminazione con SweetAlert2
         * @param {string} formId - ID del form da sottomettere dopo conferma
         * @param {string} titolo - Titolo personalizzato (opzionale)
         * @param {string} testo - Testo personalizzato (opzionale)
         */
        function confermaEliminazione(formId, titolo = 'Sei sicuro?', testo = 'Questa azione non può essere annullata!') {
            Swal.fire({
                title: titolo,
                text: testo,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E91E8C',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        /**
         * Conferma azione generica con SweetAlert2
         * @param {string} formId - ID del form da sottomettere
         * @param {string} titolo - Titolo della conferma
         * @param {string} testo - Testo della conferma
         * @param {string} bottoneConferma - Testo bottone conferma (opzionale)
         */
        function confermaAzione(formId, titolo, testo, bottoneConferma = 'Conferma') {
            Swal.fire({
                title: titolo,
                text: testo,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7B2869',
                cancelButtonColor: '#6b7280',
                confirmButtonText: bottoneConferma,
                cancelButtonText: 'Annulla',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        /**
         * Conferma con input personalizzato
         * @param {string} formId - ID del form
         * @param {string} titolo - Titolo
         * @param {string} inputPlaceholder - Placeholder input
         */
        function confermaConInput(formId, titolo, inputPlaceholder) {
            Swal.fire({
                title: titolo,
                input: 'text',
                inputPlaceholder: inputPlaceholder,
                showCancelButton: true,
                confirmButtonColor: '#7B2869',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Conferma',
                cancelButtonText: 'Annulla',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Devi inserire un valore!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    @stack('scripts')

</body>
</html>
