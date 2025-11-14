@extends('layouts.cliente')

@section('titolo', 'Dashboard')

@section('contenuto')

<!-- Welcome Message -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-2">
        Ciao {{ Auth::user()->nome }}! 👋
    </h2>
    <p class="text-gray-600">
        Benvenuta nella tua area personale MA.GIA DONNA
    </p>
</div>

<!-- 5 Bottoni Grandi (come nella demo) -->
<div class="space-y-4">
    
    <!-- 1. BALLA & SNELLA - Arancione -->
    <a href="{{ route('cliente.balla-snella') }}" 
       class="block btn-arancio rounded-2xl p-6 text-white transform hover:scale-[1.02] transition duration-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-running text-4xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Balla & Snella</h3>
                <p class="text-white text-opacity-90 text-sm">Dimagrire Ballando</p>
            </div>
            <i class="fas fa-chevron-right text-2xl"></i>
        </div>
    </a>

    <!-- 2. ALIMENTAZIONE SMART - Fucsia -->
    <a href="{{ route('cliente.alimentazione') }}" 
       class="block btn-fucsia rounded-2xl p-6 text-white transform hover:scale-[1.02] transition duration-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-utensils text-4xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Alimentazione Smart</h3>
                <p class="text-white text-opacity-90 text-sm">Piani alimentari equilibrati</p>
            </div>
            <i class="fas fa-chevron-right text-2xl"></i>
        </div>
    </a>

    <!-- 3. PELLE & BENESSERE - Viola -->
    <a href="{{ route('cliente.pelle-benessere') }}" 
       class="block btn-viola rounded-2xl p-6 text-white transform hover:scale-[1.02] transition duration-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-spa text-4xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Pelle & Benessere</h3>
                <p class="text-white text-opacity-90 text-sm">Routine di skincare</p>
            </div>
            <i class="fas fa-chevron-right text-2xl"></i>
        </div>
    </a>

    <!-- 4. COMMUNITY MA.GIA - Arancione -->
    <a href="{{ route('cliente.community') }}" 
       class="block btn-arancio rounded-2xl p-6 text-white transform hover:scale-[1.02] transition duration-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-comments text-4xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Community Ma.Gia</h3>
                <p class="text-white text-opacity-90 text-sm">Chat e supporto tra donne</p>
            </div>
            <i class="fas fa-chevron-right text-2xl"></i>
        </div>
    </a>

    <!-- 5. AREA COACHING & OPPORTUNITÀ - Fucsia -->
    <a href="{{ route('cliente.coaching') }}" 
       class="block btn-fucsia rounded-2xl p-6 text-white transform hover:scale-[1.02] transition duration-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-star text-4xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-1">Area Coaching & Opportunità</h3>
                <p class="text-white text-opacity-90 text-sm">Crescita personale e professionale</p>
            </div>
            <i class="fas fa-chevron-right text-2xl"></i>
        </div>
    </a>

</div>

<!-- Footer Quote -->
<div class="text-center mt-8 mb-4">
    <p class="text-viola-magia font-semibold text-lg italic">
        ✨ Vivere bene è la miglior magia ✨
    </p>
</div>

@endsection
