@extends('layouts.cliente')

@section('titolo', 'Balla & Snella')

@section('contenuto')

<!-- Back Button -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Sezione -->
<div class="btn-arancio rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">🏃‍♀️</div>
    <h1 class="text-4xl font-bold mb-2">Balla & Snella</h1>
    <p class="text-xl text-white text-opacity-90">Dimagrire Ballando</p>
</div>

<!-- Descrizione Programma -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-arancio-magia mr-2"></i>
        Cos'è Balla & Snella?
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
        Un programma rivoluzionario che unisce il piacere del ballo al dimagrimento efficace. 
        Balla, divertiti e ritrova la tua forma ideale in un ambiente accogliente e motivante.
    </p>
    <p class="text-gray-600 leading-relaxed">
        Le nostre lezioni sono pensate per donne di tutte le età e livelli di preparazione fisica. 
        Non serve essere delle ballerine professioniste: l'importante è lasciarsi andare e divertirsi!
    </p>
</div>

<!-- Benefici -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-heart text-arancio-magia mr-2"></i>
        I Benefici
    </h2>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-arancio-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-fire text-arancio-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Bruci Calorie</h3>
                <p class="text-sm text-gray-600">Fino a 500 calorie per lezione</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-arancio-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-smile text-arancio-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Migliora l'Umore</h3>
                <p class="text-sm text-gray-600">Rilasci endorfine naturali</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-arancio-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-arancio-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Socialità</h3>
                <p class="text-sm text-gray-600">Incontra nuove amiche</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-arancio-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-heartbeat text-arancio-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Tonificazione</h3>
                <p class="text-sm text-gray-600">Tutto il corpo si tonifica</p>
            </div>
        </div>
    </div>
</div>

<!-- Video/Foto (Placeholder) -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-images text-arancio-magia mr-2"></i>
        Guarda le Nostre Lezioni
    </h2>
    <div class="aspect-video bg-gray-200 rounded-xl flex items-center justify-center">
        <div class="text-center">
            <i class="fas fa-video text-6xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Video in arrivo!</p>
        </div>
    </div>
</div>

<!-- Testimonianze -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-quote-left text-arancio-magia mr-2"></i>
        Cosa Dicono le Nostre Clienti
    </h2>
    <div class="space-y-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"Ho perso 8 kg divertendomi! Non credevo fosse possibile!"</p>
            <p class="text-sm font-semibold text-arancio-magia">- Maria, 45 anni</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"Finalmente un'attività che non vedo l'ora di fare!"</p>
            <p class="text-sm font-semibold text-arancio-magia">- Laura, 38 anni</p>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="btn-arancio rounded-2xl p-6 text-white text-center">
    <h3 class="text-2xl font-bold mb-2">Pronta a Iniziare?</h3>
    <p class="mb-4 text-white text-opacity-90">Prenota la tua lezione di prova gratuita!</p>
    <a href="{{ route('cliente.calendario') }}" class="inline-block bg-white text-arancio-magia px-8 py-3 rounded-xl font-bold hover:shadow-xl transition">
        Prenota Ora
    </a>
</div>

@endsection
