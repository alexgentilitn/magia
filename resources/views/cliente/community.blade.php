@extends('layouts.cliente')

@section('titolo', 'Community Ma.Gia')

@section('contenuto')

<!-- Back Button -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Sezione -->
<div class="btn-arancio rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">💬</div>
    <h1 class="text-4xl font-bold mb-2">Community Ma.Gia</h1>
    <p class="text-xl text-white text-opacity-90">Chat e Supporto tra Donne</p>
</div>

<!-- Descrizione Community -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-arancio-magia mr-2"></i>
        Benvenuta nella Community
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
        La Community MA.GIA DONNA è uno spazio sicuro e accogliente dove donne come te si incontrano, 
        si supportano e condividono il loro percorso verso il benessere.
    </p>
    <p class="text-gray-600 leading-relaxed">
        Qui puoi fare domande, condividere i tuoi successi, trovare motivazione e creare amicizie autentiche.
    </p>
</div>

<!-- Cosa Trovi nella Community -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-star text-arancio-magia mr-2"></i>
        Cosa Trovi Qui
    </h2>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-comments text-arancio-magia text-2xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Chat di Gruppo</h3>
                    <p class="text-sm text-gray-600">Confrontati con altre donne del percorso</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-question-circle text-arancio-magia text-2xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">FAQ e Risposte</h3>
                    <p class="text-sm text-gray-600">Trova risposte alle domande più comuni</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-lightbulb text-arancio-magia text-2xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Consigli & Tips</h3>
                    <p class="text-sm text-gray-600">Trucchi e suggerimenti dal team</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-trophy text-arancio-magia text-2xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Sfide e Obiettivi</h3>
                    <p class="text-sm text-gray-600">Partecipa alle sfide mensili</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Principale (Coming Soon) -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-comment-dots text-arancio-magia mr-2"></i>
        Chat Community
    </h2>
    <div class="bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl p-12 text-center">
        <div class="max-w-md mx-auto">
            <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Chat in Arrivo!</h3>
            <p class="text-gray-600 mb-4">
                Stiamo preparando uno spazio ancora più interattivo per la nostra community. 
                A breve potrai chattare in tempo reale!
            </p>
            <div class="inline-flex items-center space-x-2 text-arancio-magia font-semibold">
                <i class="fas fa-rocket"></i>
                <span>Prossimamente</span>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Popolari -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-question-circle text-arancio-magia mr-2"></i>
        FAQ - Domande Frequenti
    </h2>
    <div class="space-y-3">
        <details class="bg-gray-50 rounded-xl p-4">
            <summary class="font-semibold text-gray-800 cursor-pointer">
                Come faccio a prenotare una lezione?
            </summary>
            <p class="text-sm text-gray-600 mt-2 pl-4">
                Vai nella sezione Calendario e scegli il corso che preferisci. Clicca su "Prenota" e conferma la tua presenza!
            </p>
        </details>
        
        <details class="bg-gray-50 rounded-xl p-4">
            <summary class="font-semibold text-gray-800 cursor-pointer">
                Posso cambiare programma?
            </summary>
            <p class="text-sm text-gray-600 mt-2 pl-4">
                Certo! Contatta il nostro team dal tuo profilo e ti aiuteremo a scegliere il programma più adatto a te.
            </p>
        </details>
        
        <details class="bg-gray-50 rounded-xl p-4">
            <summary class="font-semibold text-gray-800 cursor-pointer">
                Come funziona il piano alimentare?
            </summary>
            <p class="text-sm text-gray-600 mt-2 pl-4">
                Dopo la prima consulenza, riceverai un piano personalizzato con menu settimanali e ricette. 
                Il piano viene aggiornato in base ai tuoi progressi.
            </p>
        </details>
        
        <details class="bg-gray-50 rounded-xl p-4">
            <summary class="font-semibold text-gray-800 cursor-pointer">
                Posso portare un'amica?
            </summary>
            <p class="text-sm text-gray-600 mt-2 pl-4">
                Assolutamente sì! Anzi, guadagni vantaggi con il nostro programma referral. 
                Scopri di più nella sezione Coaching & Opportunità!
            </p>
        </details>
    </div>
</div>

<!-- Supporto Diretto -->
<div class="bg-gradient-to-r from-arancio-magia to-fucsia-magia rounded-2xl p-6 text-white mb-6">
    <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
            <i class="fas fa-headset text-6xl"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold mb-2">Hai Bisogno di Aiuto?</h3>
            <p class="text-white text-opacity-90 mb-3">
                Il nostro team è sempre disponibile per supportarti nel tuo percorso.
            </p>
            <a href="{{ route('cliente.profilo') }}" class="inline-block bg-white text-arancio-magia px-6 py-2 rounded-lg font-bold text-sm hover:shadow-xl transition">
                Contatta il Team
            </a>
        </div>
    </div>
</div>

<!-- Ultime Novità -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-bullhorn text-arancio-magia mr-2"></i>
        Ultime Novità
    </h2>
    <div class="space-y-3">
        <div class="border-l-4 border-arancio-magia pl-4 py-2">
            <p class="font-semibold text-gray-800">Nuove lezioni di Zumba!</p>
            <p class="text-sm text-gray-600">Lunedì e Giovedì ore 19:00</p>
        </div>
        <div class="border-l-4 border-fucsia-magia pl-4 py-2">
            <p class="font-semibold text-gray-800">Consulenze nutrizionali gratuite</p>
            <p class="text-sm text-gray-600">Prenota la tua dal 15 al 30 di questo mese</p>
        </div>
        <div class="border-l-4 border-viola-magia pl-4 py-2">
            <p class="font-semibold text-gray-800">Nuova linea prodotti in arrivo</p>
            <p class="text-sm text-gray-600">Skincare 100% naturale disponibile da Dicembre</p>
        </div>
    </div>
</div>

@endsection
