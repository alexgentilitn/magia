<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titolo', 'Area Professionista') - MA.GIA DONNA</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 per notifiche belle -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js per grafici professionali -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#7B2869',
                        'fucsia-magia': '#E91E8C',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Personalizza SweetAlert con i colori del brand */
        .swal2-popup {
            font-family: inherit;
        }
        .swal2-confirm {
            background: linear-gradient(to right, #7B2869, #E91E8C) !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">

    <!-- NOTIFICHE CON SWEETALERT2 -->
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

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-violet-600 to-purple-700 text-white flex-shrink-0 hidden md:block">
            <div class="flex flex-col h-full">

                <div class="p-6 border-b border-white border-opacity-20">
                    <a href="{{ route('professionista.dashboard') }}">
                        <h1 class="text-2xl font-bold">
                            <span class="text-white">MA.GIA</span>
                            <span class="text-purple-200">DONNA</span>
                        </h1>
                    </a>
                    <p class="text-purple-100 text-xs mt-2">Area Professionista</p>
                </div>

                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="space-y-1 px-3">

                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('professionista.dashboard') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.dashboard')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="px-4 py-2">
                            <div class="border-t border-white border-opacity-20"></div>
                            <p class="text-xs text-purple-200 mt-2 font-semibold uppercase">Area Personale</p>
                        </li>

                        <!-- Agenda (Lezioni) -->
                        <li>
                            <a href="{{ route('professionista.lezioni.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.lezioni.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-list-ul w-5"></i>
                                <span class="ml-3 font-medium">Agenda Lezioni</span>
                            </a>
                        </li>

                        <!-- Calendario -->
                        <li>
                            <a href="{{ route('professionista.calendario.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.calendario.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-calendar-alt w-5"></i>
                                <span class="ml-3 font-medium">Calendario</span>
                            </a>
                        </li>

                        <!-- Compensi -->
                        <li>
                            <a href="{{ route('professionista.compensi.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.compensi.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-euro-sign w-5"></i>
                                <span class="ml-3 font-medium">Compensi</span>
                            </a>
                        </li>

                        <!-- Disponibilità -->
                        <li>
                            <a href="{{ route('professionista.disponibilita.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.disponibilita.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-clock w-5"></i>
                                <span class="ml-3 font-medium">Disponibilità</span>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="px-4 py-2">
                            <div class="border-t border-white border-opacity-20"></div>
                        </li>

                        <!-- Profilo -->
                        <li>
                            <a href="{{ route('professionista.profilo.index') }}"
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('professionista.profilo.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-user-circle w-5"></i>
                                <span class="ml-3 font-medium">Profilo</span>
                            </a>
                        </li>

                    </ul>
                </nav>

                <div class="p-4 border-t border-white border-opacity-20">
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-violet-600 font-bold">
                            {{ substr(auth()->user()->nome, 0, 1) }}{{ substr(auth()->user()->cognome, 0, 1) }}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->nome }} {{ auth()->user()->cognome }}</p>
                            <p class="text-xs text-purple-200 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Esci
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">
                            @yield('titolo', 'Dashboard')
                        </h2>
                        @hasSection('sottotitolo')
                        <p class="text-sm text-gray-600 mt-1">
                            @yield('sottotitolo')
                        </p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- User Info Mobile -->
                        <div class="md:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                        </div>

                        <!-- Notifiche -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    0
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

</body>
</html>
