<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titolo', 'Admin') - MA.GIA DONNA</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">

    <!-- MESSAGGIO SUCCESS CON ALERT JAVASCRIPT -->
    @if(session('success'))
    <script>
        alert("✅ SUCCESSO: {{ session('success') }}");
    </script>
    @endif

    <!-- MESSAGGIO ERROR CON ALERT JAVASCRIPT -->
    @if(session('error'))
    <script>
        alert("❌ ERRORE: {{ session('error') }}");
    </script>
    @endif
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-viola-magia to-fucsia-magia text-white flex-shrink-0 hidden md:block">
            <div class="flex flex-col h-full">
                
                <div class="p-6 border-b border-white border-opacity-20">
                    <a href="{{ route('admin.dashboard') }}">
                        <h1 class="text-2xl font-bold">
                            <span class="text-white">Ma.Gia</span>
                            <span class="text-pink-200">DONNA</span>
                        </h1>
                    </a>
                    <p class="text-pink-100 text-xs mt-2">Pannello Amministratore</p>
                </div>

                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="space-y-1 px-3">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.dashboard')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.clienti.index') }}" 
                               class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition @if(request()->routeIs('admin.clienti.*')) bg-white bg-opacity-20 @endif">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3 font-medium">Clienti</span>
                            </a>
                        </li>
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
                            <span class="text-viola-magia">Ma.Gia</span>
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
                    <span class="text-white">Ma.Gia</span>
                    <span class="text-pink-200">DONNA</span>
                </h1>
                <button @click="open = false" class="text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex-1 py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.clienti.index') }}" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                            <i class="fas fa-users w-5"></i>
                            <span class="ml-3">Clienti</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-white border-opacity-20">
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

    @stack('scripts')

</body>
</html>