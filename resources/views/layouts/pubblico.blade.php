<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MA.GIA DONNA - Benessere, energia e bellezza al femminile. La tua energia, la nostra missione.">
    <meta name="keywords" content="benessere donna, fitness femminile, Balla e Snella, wellness, Bolzano">
    <title>@yield('titolo', 'Benessere al Femminile') - MA.GIA DONNA</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Colori Brand -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#7B2869',
                        'fucsia-magia': '#E91E8C',
                        'arancio-magia': '#FF6B35',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="bg-white">
    
    <!-- Header Pubblico -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="Ma.Gia srl - Wellness for Women" 
                             class="h-16 w-auto">
                    </a>
                </div>

                <!-- Menu Desktop -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-fucsia-magia transition font-medium">
                        Home
                    </a>
                    <a href="/#programmi" class="text-gray-700 hover:text-fucsia-magia transition font-medium">
                        Programmi
                    </a>
                    <a href="/#chi-siamo" class="text-gray-700 hover:text-fucsia-magia transition font-medium">
                        Chi Siamo
                    </a>
                    <a href="/#contatti" class="text-gray-700 hover:text-fucsia-magia transition font-medium">
                        Contatti
                    </a>
                    <a href="{{ route('cliente.login') }}" class="text-gray-700 hover:text-fucsia-magia transition font-medium">
                        <i class="fas fa-user mr-1"></i> Accedi
                    </a>
                    <a href="{{ route('registrazione') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-full hover:shadow-lg transition transform hover:-translate-y-0.5 font-semibold">
                        Iscriviti Ora
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-600" x-data @click="$dispatch('toggle-mobile-menu')">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-data="{ open: false }" 
             @toggle-mobile-menu.window="open = !open"
             x-show="open"
             x-cloak
             class="md:hidden bg-white border-t border-gray-200">
            <nav class="px-4 py-4 space-y-3">
                <a href="/" class="block py-2 text-gray-700 hover:text-fucsia-magia font-medium">
                    Home
                </a>
                <a href="/#programmi" class="block py-2 text-gray-700 hover:text-fucsia-magia font-medium">
                    Programmi
                </a>
                <a href="/#chi-siamo" class="block py-2 text-gray-700 hover:text-fucsia-magia font-medium">
                    Chi Siamo
                </a>
                <a href="/#contatti" class="block py-2 text-gray-700 hover:text-fucsia-magia font-medium">
                    Contatti
                </a>
                <hr class="my-3">
                <a href="{{ route('cliente.login') }}" class="block py-2 text-gray-700 hover:text-fucsia-magia font-medium">
                    <i class="fas fa-user mr-2"></i> Accedi
                </a>
                <a href="{{ route('registrazione') }}" 
                   class="block text-center py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg font-semibold">
                    Iscriviti Ora
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('contenuto')
    </main>

    <!-- Footer Pubblico -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Info MA.GIA DONNA -->
                <div>
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Ma.Gia srl" 
                         class="h-20 w-auto rounded-lg mb-4">
                    <p class="text-gray-400 text-sm mt-4">
                        <strong class="text-white">Wellness for Women</strong><br>
                        La tua energia, la nostra missione.<br>
                        Il tuo percorso verso una vita più sana e felice.
                    </p>
                </div>

                <!-- Link Rapidi -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Link Utili</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="/#programmi" class="text-gray-400 hover:text-fucsia-magia transition">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>I Nostri Programmi
                            </a>
                        </li>
                        <li>
                            <a href="/#chi-siamo" class="text-gray-400 hover:text-fucsia-magia transition">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>Chi Siamo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('registrazione') }}" class="text-gray-400 hover:text-fucsia-magia transition">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>Iscriviti
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cliente.login') }}" class="text-gray-400 hover:text-fucsia-magia transition">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>Area Clienti
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contatti -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Contatti</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-fucsia-magia"></i>
                            <span>Via Roma 123<br>39100 Bolzano, Italia</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-fucsia-magia"></i>
                            <a href="tel:+390471123456" class="hover:text-fucsia-magia transition">
                                +39 0471 123456
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-fucsia-magia"></i>
                            <a href="mailto:info@magiadonna.it" class="hover:text-fucsia-magia transition">
                                info@magiadonna.it
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Seguici</h3>
                    <div class="flex space-x-3 mb-6">
                        <a href="#" class="h-10 w-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-fucsia-magia transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="h-10 w-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-fucsia-magia transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="h-10 w-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-fucsia-magia transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="h-10 w-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-fucsia-magia transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                    <p class="text-sm text-gray-400 mb-2">Iscriviti alla Newsletter</p>
                    <form class="flex">
                        <input type="email" 
                               placeholder="La tua email"
                               class="flex-1 px-4 py-2 rounded-l-lg bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-fucsia-magia text-sm">
                        <button type="submit" 
                                class="px-4 py-2 bg-fucsia-magia rounded-r-lg hover:bg-viola-magia transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-400">
                <p>© {{ date('Y') }} Ma.Gia srl - Wellness for Women - P.IVA 12345678901 - Tutti i diritti riservati</p>
                <div class="mt-2 space-x-4">
                    <a href="#" class="hover:text-fucsia-magia transition">Privacy Policy</a>
                    <span>•</span>
                    <a href="#" class="hover:text-fucsia-magia transition">Cookie Policy</a>
                    <span>•</span>
                    <a href="#" class="hover:text-fucsia-magia transition">Termini e Condizioni</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <style>
        [x-cloak] { display: none !important; }
    </style>

</body>
</html>
