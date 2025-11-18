{{--
    LAYOUT ADMIN MINIMAL

    Questo layout è usato SOLO per routes di debug in ambiente local.
    NON usare per pagine di produzione - usa layouts/admin.blade.php invece.

    View che usano questo layout:
    - admin/report/debug-minimal.blade.php (route debug)
--}}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - MA.GIA DONNA</title>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-viola-magia to-fucsia-magia text-white flex-shrink-0">
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
                                <i class="fas fa-chart-bar w-5"></i>
                                <span class="ml-3 font-medium">Report</span>
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
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.clienti.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3 font-medium">Clienti</span>
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

                        @if(Auth::user()->tipo_utente === 'amministratore')
                        <!-- Impostazioni (solo amministratori) -->
                        <li>
                            <a href="{{ route('admin.impostazioni.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.impostazioni.*') || request()->routeIs('admin.impostazioni-sistema.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-cog w-5"></i>
                                <span class="ml-3 font-medium">Impostazioni</span>
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
            <div class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
