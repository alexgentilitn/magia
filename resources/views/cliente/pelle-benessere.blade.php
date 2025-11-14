@extends('layouts.cliente')

@section('titolo', 'Pelle & Benessere')

@section('contenuto')

<!-- Back Button -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Sezione -->
<div class="btn-viola rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">✨</div>
    <h1 class="text-4xl font-bold mb-2">Pelle & Benessere</h1>
    <p class="text-xl text-white text-opacity-90">Routine di Skincare</p>
</div>

<!-- Descrizione Programma -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-viola-magia mr-2"></i>
        La Bellezza Inizia da Dentro
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
        Prendersi cura della propria pelle non è solo una questione estetica, ma un atto d'amore verso te stessa. 
        I nostri prodotti e trattamenti sono selezionati con cura per offrirti il meglio del benessere.
    </p>
    <p class="text-gray-600 leading-relaxed">
        Scopri la nostra gamma di prodotti naturali e le routine personalizzate 
        per ogni tipo di pelle ed esigenza.
    </p>
</div>

<!-- Categorie Prodotti -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-th-large text-viola-magia mr-2"></i>
        Le Nostre Linee
    </h2>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="border-2 border-viola-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-viola-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sun text-viola-magia text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Viso & Collo</h3>
                    <p class="text-sm text-gray-600">Creme, sieri e maschere</p>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-viola-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-viola-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-sparkles text-viola-magia text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Corpo</h3>
                    <p class="text-sm text-gray-600">Creme rassodanti e anticellulite</p>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-viola-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-viola-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-viola-magia text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Contorno Occhi</h3>
                    <p class="text-sm text-gray-600">Trattamenti specifici anti-età</p>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-viola-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-viola-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-leaf text-viola-magia text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Naturali & Bio</h3>
                    <p class="text-sm text-gray-600">Ingredienti 100% naturali</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- I Nostri Ingredienti -->
<div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-2xl p-6 text-white mb-6">
    <h3 class="text-2xl font-bold mb-4">Ingredienti Selezionati</h3>
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <i class="fas fa-seedling text-3xl mb-2"></i>
            <p class="font-semibold">Acido Ialuronico</p>
            <p class="text-sm text-white text-opacity-90">Idratazione profonda</p>
        </div>
        <div>
            <i class="fas fa-seedling text-3xl mb-2"></i>
            <p class="font-semibold">Vitamina C</p>
            <p class="text-sm text-white text-opacity-90">Luminosità e tono</p>
        </div>
        <div>
            <i class="fas fa-seedling text-3xl mb-2"></i>
            <p class="font-semibold">Collagene</p>
            <p class="text-sm text-white text-opacity-90">Elasticità e tonicità</p>
        </div>
    </div>
</div>

<!-- Routine Consigliata -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-clock text-viola-magia mr-2"></i>
        La Tua Routine Quotidiana
    </h2>
    <div class="space-y-4">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">
                ☀️
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Mattina</h3>
                <p class="text-sm text-gray-600">Detergente • Tonico • Siero Vit.C • Crema Giorno SPF</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                🌙
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Sera</h3>
                <p class="text-sm text-gray-600">Struccante • Detergente • Tonico • Siero • Crema Notte</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 w-10 h-10 bg-fucsia-magia rounded-full flex items-center justify-center text-white font-bold">
                ✨
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Settimanale</h3>
                <p class="text-sm text-gray-600">Scrub 2x • Maschera Purificante 1x • Maschera Idratante 1x</p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonianze -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-quote-left text-viola-magia mr-2"></i>
        Le Recensioni
    </h2>
    <div class="space-y-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"La mia pelle non è mai stata così luminosa! Prodotti fantastici."</p>
            <p class="text-sm font-semibold text-viola-magia">- Elena, 48 anni</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"Finalmente una routine semplice ma efficace!"</p>
            <p class="text-sm font-semibold text-viola-magia">- Sara, 32 anni</p>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="btn-viola rounded-2xl p-6 text-white text-center">
    <h3 class="text-2xl font-bold mb-2">Scopri i Nostri Prodotti</h3>
    <p class="mb-4 text-white text-opacity-90">Richiedi una consulenza personalizzata gratuita</p>
    <a href="{{ route('cliente.profilo') }}" class="inline-block bg-white text-viola-magia px-8 py-3 rounded-xl font-bold hover:shadow-xl transition">
        Richiedi Consulenza
    </a>
</div>

@endsection
