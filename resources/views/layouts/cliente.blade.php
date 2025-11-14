<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7B2869">
    <title>@yield('titolo', 'Area Cliente') - MA.GIA DONNA</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#7B2869',
                        'fucsia-magia': '#E91E8C',
                        'arancio-magia': '#FF6B35',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            padding-bottom: 80px;
        }
        
        .gradient-magia {
            background: linear-gradient(135deg, #7B2869 0%, #E91E8C 50%, #FF6B35 100%);
        }
        
        .btn-arancio {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8A5C 100%);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        
        .btn-fucsia {
            background: linear-gradient(135deg, #E91E8C 0%, #FF1E9B 100%);
            box-shadow: 0 4px 15px rgba(233, 30, 140, 0.3);
        }
        
        .btn-viola {
            background: linear-gradient(135deg, #7B2869 0%, #9B3689 100%);
            box-shadow: 0 4px 15px rgba(123, 40, 105, 0.3);
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 0;
            color: #9CA3AF;
            transition: all 0.3s;
        }
        
        .nav-item.active {
            color: #E91E8C;
        }
        
        .nav-item i {
            font-size: 24px;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 500;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl font-bold">
                        <span class="text-viola-magia">Ma.Gia</span>
                        <span class="text-fucsia-magia">DONNA</span>
                    </h1>
                    <div class="text-fucsia-magia text-3xl">🦋</div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifiche -->
                    <button class="relative p-2 text-gray-600 hover:text-fucsia-magia">
                        <i class="far fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- Profilo -->
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->nome, 0, 1)) }}
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->nome }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->tipo_utente) }}</p>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Tagline -->
            <p class="text-center text-viola-magia font-medium mt-2 text-sm md:text-base">
                Benessere, energia e bellezza al femminile
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('contenuto')
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav md:hidden">
        <div class="flex justify-around">
            
            <a href="{{ route('cliente.dashboard') }}" class="nav-item {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <a href="{{ route('cliente.programmi') }}" class="nav-item {{ request()->routeIs('cliente.programmi') ? 'active' : '' }}">
                <i class="fas fa-dumbbell"></i>
                <span>Programmi</span>
            </a>
            
            <a href="{{ route('cliente.calendario') }}" class="nav-item {{ request()->routeIs('cliente.calendario') ? 'active' : '' }}">
                <i class="far fa-calendar"></i>
                <span>Calendario</span>
            </a>
            
            <a href="{{ route('cliente.profilo') }}" class="nav-item {{ request()->routeIs('cliente.profilo') ? 'active' : '' }}">
                <i class="far fa-user"></i>
                <span>Profilo</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="nav-item" style="flex: 1;">
                @csrf
                <button type="submit" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Esci</span>
                </button>
            </form>
            
        </div>
    </nav>

    @stack('scripts')

</body>
</html>
