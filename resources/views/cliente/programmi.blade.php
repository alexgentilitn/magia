@extends('layouts.cliente')

@section('titolo', 'I Miei Programmi')

@section('contenuto')

<!-- Back Button (solo mobile) -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium md:hidden">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header -->
<div class="gradient-magia rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">🎯</div>
    <h1 class="text-4xl font-bold mb-2">I Miei Programmi</h1>
    <p class="text-xl text-white text-opacity-90">Il tuo percorso personalizzato verso il benessere</p>
</div>

<!-- Programma Attivo -->
@if(Auth::user()->cliente->programma_attuale)
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-fire text-arancio-magia mr-2"></i>
            Programma Attivo
        </h2>
        <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-bold">
            <i class="fas fa-check-circle mr-1"></i> Attivo
        </span>
    </div>

    <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-xl p-6 text-white mb-6">
        <h3 class="text-3xl font-bold mb-4">{{ Auth::user()->cliente->programma_attuale }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="flex items-center space-x-3">
                <i class="far fa-calendar-alt text-2xl"></i>
                <div>
                    <p class="text-sm opacity-90">Data Inizio</p>
                    <p class="font-semibold">{{ Auth::user()->cliente->inizio_programma ? Auth::user()->cliente->inizio_programma->format('d/m/Y') : 'Non specificata' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <i class="far fa-calendar-check text-2xl"></i>
                <div>
                    <p class="text-sm opacity-90">Data Fine Prevista</p>
                    <p class="font-semibold">{{ Auth::user()->cliente->fine_programma ? Auth::user()->cliente->fine_programma->format('d/m/Y') : 'Non specificata' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->cliente->inizio_programma && Auth::user()->cliente->fine_programma)
    <!-- Progress Bar -->
    @php
        $oggi = \Carbon\Carbon::now();
        $inizio = Auth::user()->cliente->inizio_programma;
        $fine = Auth::user()->cliente->fine_programma;
        $totaleGiorni = $inizio->diffInDays($fine);
        $giorniTrascorsi = $inizio->diffInDays($oggi);
        $percentuale = $totaleGiorni > 0 ? min(100, ($giorniTrascorsi / $totaleGiorni) * 100) : 0;
    @endphp

    <div class="mb-6">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-semibold text-gray-700">Progressione Programma</span>
            <span class="text-sm font-bold text-fucsia-magia">{{ number_format($percentuale, 0) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full transition-all duration-500" style="width: {{ $percentuale }}%"></div>
        </div>
        <div class="flex justify-between mt-1 text-xs text-gray-500">
            <span>Giorni trascorsi: {{ $giorniTrascorsi }}</span>
            <span>Giorni rimanenti: {{ max(0, $totaleGiorni - $giorniTrascorsi) }}</span>
        </div>
    </div>
    @endif

    <!-- Azioni Rapide -->
    <div class="grid md:grid-cols-3 gap-4">
        <a href="{{ route('cliente.calendario') }}" class="flex items-center space-x-3 p-4 bg-arancio-magia bg-opacity-10 rounded-xl hover:bg-opacity-20 transition">
            <div class="w-12 h-12 bg-arancio-magia rounded-lg flex items-center justify-center text-white">
                <i class="far fa-calendar text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Calendario</p>
                <p class="text-xs text-gray-600">Vedi lezioni</p>
            </div>
        </a>

        <a href="{{ route('messaging.index') }}" class="flex items-center space-x-3 p-4 bg-fucsia-magia bg-opacity-10 rounded-xl hover:bg-opacity-20 transition">
            <div class="w-12 h-12 bg-fucsia-magia rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-comments text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Chat Coach</p>
                <p class="text-xs text-gray-600">Contatta</p>
            </div>
        </a>

        <a href="{{ route('cliente.profilo') }}" class="flex items-center space-x-3 p-4 bg-viola-magia bg-opacity-10 rounded-xl hover:bg-opacity-20 transition">
            <div class="w-12 h-12 bg-viola-magia rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Progressi</p>
                <p class="text-xs text-gray-600">Vedi misure</p>
            </div>
        </a>
    </div>
</div>
@else
<!-- Nessun Programma Attivo -->
<div class="bg-white rounded-2xl shadow-sm p-12 mb-6 text-center">
    <div class="text-6xl mb-4">📋</div>
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Nessun Programma Attivo</h2>
    <p class="text-gray-600 mb-6">Non hai ancora un programma assegnato. Esplora le nostre offerte o contatta la tua coach!</p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('cliente.dashboard') }}" class="inline-block bg-fucsia-magia text-white px-6 py-3 rounded-xl font-bold hover:shadow-xl transition">
            <i class="fas fa-home mr-2"></i>
            Esplora Programmi
        </a>
        <a href="{{ route('messaging.index') }}" class="inline-block bg-white border-2 border-fucsia-magia text-fucsia-magia px-6 py-3 rounded-xl font-bold hover:shadow-xl transition">
            <i class="fas fa-comments mr-2"></i>
            Contatta Coach
        </a>
    </div>
</div>
@endif

<!-- Tutti i Programmi Disponibili -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-list text-fucsia-magia mr-2"></i>
        Programmi Disponibili
    </h2>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Programma 1: Balla & Snella -->
        <div class="border-2 border-arancio-magia rounded-xl overflow-hidden hover:shadow-xl transition">
            <div class="bg-arancio-magia p-6 text-white">
                <div class="text-4xl mb-3">🏃‍♀️</div>
                <h3 class="text-2xl font-bold">Balla & Snella</h3>
                <p class="text-white text-opacity-90 text-sm">Dimagrire ballando</p>
            </div>
            <div class="p-6">
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-arancio-magia mt-1"></i>
                        <span class="text-sm text-gray-700">3 lezioni settimanali di danza fitness</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-arancio-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Programma dimagrimento personalizzato</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-arancio-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Monitoraggio progressi mensile</span>
                    </li>
                </ul>
                <a href="{{ route('cliente.balla-snella') }}" class="block text-center bg-arancio-magia text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition">
                    Scopri di più
                </a>
            </div>
        </div>

        <!-- Programma 2: Alimentazione Smart -->
        <div class="border-2 border-fucsia-magia rounded-xl overflow-hidden hover:shadow-xl transition">
            <div class="bg-fucsia-magia p-6 text-white">
                <div class="text-4xl mb-3">🥗</div>
                <h3 class="text-2xl font-bold">Alimentazione Smart</h3>
                <p class="text-white text-opacity-90 text-sm">Piani alimentari equilibrati</p>
            </div>
            <div class="p-6">
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Piano alimentare personalizzato</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Ricette settimanali facili</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Consulenza nutrizionale mensile</span>
                    </li>
                </ul>
                <a href="{{ route('cliente.alimentazione') }}" class="block text-center bg-fucsia-magia text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition">
                    Scopri di più
                </a>
            </div>
        </div>

        <!-- Programma 3: Pelle & Benessere -->
        <div class="border-2 border-viola-magia rounded-xl overflow-hidden hover:shadow-xl transition">
            <div class="bg-viola-magia p-6 text-white">
                <div class="text-4xl mb-3">✨</div>
                <h3 class="text-2xl font-bold">Pelle & Benessere</h3>
                <p class="text-white text-opacity-90 text-sm">Routine di skincare</p>
            </div>
            <div class="p-6">
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-viola-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Routine skincare personalizzata</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-viola-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Prodotti naturali consigliati</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-viola-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Trattamenti mensili inclusi</span>
                    </li>
                </ul>
                <a href="{{ route('cliente.pelle-benessere') }}" class="block text-center bg-viola-magia text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition">
                    Scopri di più
                </a>
            </div>
        </div>

        <!-- Programma 4: Coaching Completo -->
        <div class="border-2 border-gradient-to-r from-viola-magia to-fucsia-magia rounded-xl overflow-hidden hover:shadow-xl transition relative">
            <div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                CONSIGLIATO
            </div>
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia p-6 text-white">
                <div class="text-4xl mb-3">👑</div>
                <h3 class="text-2xl font-bold">Coaching Completo</h3>
                <p class="text-white text-opacity-90 text-sm">Tutto incluso</p>
            </div>
            <div class="p-6">
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Tutti i programmi inclusi</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Coach personale dedicata</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-check text-fucsia-magia mt-1"></i>
                        <span class="text-sm text-gray-700">Accesso community premium</span>
                    </li>
                </ul>
                <a href="{{ route('cliente.coaching') }}" class="block text-center bg-gradient-to-r from-viola-magia to-fucsia-magia text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition">
                    Scopri di più
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Programmi -->
<div class="bg-white rounded-2xl shadow-sm p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-question-circle text-arancio-magia mr-2"></i>
        Domande Frequenti
    </h2>

    <div class="space-y-4">
        <details class="group bg-gray-50 rounded-lg">
            <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-gray-800">
                <span>Come faccio a iscrivermi a un programma?</span>
                <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
                <p>Puoi contattare direttamente la tua coach tramite la chat interna o esplorare i dettagli di ogni programma cliccando su "Scopri di più".</p>
            </div>
        </details>

        <details class="group bg-gray-50 rounded-lg">
            <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-gray-800">
                <span>Posso combinare più programmi?</span>
                <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
                <p>Assolutamente sì! Il programma "Coaching Completo" include tutti i nostri servizi. In alternativa, puoi creare un pacchetto personalizzato parlando con la tua coach.</p>
            </div>
        </details>

        <details class="group bg-gray-50 rounded-lg">
            <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-gray-800">
                <span>Quanto dura un programma?</span>
                <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
                <p>La durata varia in base al programma scelto e ai tuoi obiettivi personali. Generalmente i programmi durano da 3 a 6 mesi, ma possono essere personalizzati.</p>
            </div>
        </details>
    </div>
</div>

<!-- Footer Quote -->
<div class="text-center mt-8 mb-4">
    <p class="text-viola-magia font-semibold text-lg italic">
        ✨ Il tuo benessere è la nostra missione ✨
    </p>
</div>

@endsection
