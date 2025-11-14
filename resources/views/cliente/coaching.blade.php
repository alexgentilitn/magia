@extends('layouts.cliente')

@section('titolo', 'Coaching & Opportunità')

@section('contenuto')

<!-- Back Button -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Sezione -->
<div class="btn-fucsia rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">⭐</div>
    <h1 class="text-4xl font-bold mb-2">Coaching & Opportunità</h1>
    <p class="text-xl text-white text-opacity-90">Crescita Personale e Professionale</p>
</div>

<!-- Descrizione Sezione -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
        Cresci con Ma.Gia DONNA
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
        MA.GIA DONNA non è solo benessere fisico: crediamo nella crescita personale e professionale delle donne. 
        Qui trovi opportunità di sviluppo, networking e guadagno.
    </p>
    <p class="text-gray-600 leading-relaxed">
        Trasforma la tua passione in opportunità concrete!
    </p>
</div>

<!-- Programma Referral -->
<div class="bg-gradient-to-r from-fucsia-magia to-viola-magia rounded-2xl p-6 text-white mb-6">
    <div class="text-center mb-6">
        <i class="fas fa-gift text-6xl mb-3"></i>
        <h2 class="text-3xl font-bold mb-2">Programma Referral</h2>
        <p class="text-xl text-white text-opacity-90">Guadagna portando le tue amiche!</p>
    </div>
    
    <div class="bg-white bg-opacity-10 rounded-xl p-6 mb-4">
        <h3 class="text-2xl font-bold mb-3">Come Funziona</h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 bg-white rounded-full flex items-center justify-center text-fucsia-magia font-bold">
                    1
                </div>
                <p class="text-white text-opacity-90">Invita un'amica a iscriversi con il tuo codice</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 bg-white rounded-full flex items-center justify-center text-fucsia-magia font-bold">
                    2
                </div>
                <p class="text-white text-opacity-90">Lei riceve uno sconto del 20% sul primo mese</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 bg-white rounded-full flex items-center justify-center text-fucsia-magia font-bold">
                    3
                </div>
                <p class="text-white text-opacity-90">Tu ricevi un buono di 30€ da spendere in servizi o prodotti</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 text-center">
        <p class="text-gray-800 font-bold text-xl mb-2">Il Tuo Codice Invito</p>
        <div class="bg-gray-100 rounded-lg p-4 mb-3">
            <p class="text-3xl font-black text-fucsia-magia tracking-wider">MAGIA2025</p>
        </div>
        <button class="bg-fucsia-magia text-white px-6 py-2 rounded-lg font-bold hover:shadow-lg transition">
            <i class="fas fa-copy mr-2"></i>Copia Codice
        </button>
    </div>
</div>

<!-- Opportunità di Collaborazione -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-handshake text-fucsia-magia mr-2"></i>
        Diventa Partner
    </h2>
    <p class="text-gray-600 mb-4">
        Sei appassionata di benessere e vorresti collaborare con noi? 
        Scopri le opportunità per diventare ambasciatrice MA.GIA DONNA!
    </p>
    
    <div class="grid md:grid-cols-2 gap-4">
        <div class="border-2 border-fucsia-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition">
            <div class="flex items-start space-x-3">
                <i class="fas fa-user-tie text-fucsia-magia text-3xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Ambasciatrice</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        Rappresenta il nostro brand sui social e guadagna commissioni
                    </p>
                    <div class="text-fucsia-magia font-semibold text-sm">
                        Commissioni fino al 20%
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-fucsia-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition">
            <div class="flex items-start space-x-3">
                <i class="fas fa-graduation-cap text-fucsia-magia text-3xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Istruttrice</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        Hai qualifiche nel fitness? Unisciti al nostro team
                    </p>
                    <div class="text-fucsia-magia font-semibold text-sm">
                        Contratto collaborazione
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-fucsia-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition">
            <div class="flex items-start space-x-3">
                <i class="fas fa-store text-fucsia-magia text-3xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Rivenditrice Prodotti</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        Vendi i nostri prodotti e guadagna
                    </p>
                    <div class="text-fucsia-magia font-semibold text-sm">
                        Margini fino al 30%
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-fucsia-magia border-opacity-20 rounded-xl p-4 hover:border-opacity-100 transition">
            <div class="flex items-start space-x-3">
                <i class="fas fa-building text-fucsia-magia text-3xl"></i>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Apri una Sede</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        Porta MA.GIA DONNA nella tua città
                    </p>
                    <div class="text-fucsia-magia font-semibold text-sm">
                        Franchising disponibile
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonianze Partner -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-quote-left text-fucsia-magia mr-2"></i>
        Storie di Successo
    </h2>
    <div class="space-y-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">
                "Ho portato 15 amiche e ho guadagnato 450€ in buoni! Ora sono ambasciatrice ufficiale."
            </p>
            <p class="text-sm font-semibold text-fucsia-magia">- Martina, Ambasciatrice Gold</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">
                "Grazie a MA.GIA DONNA ho trasformato la mia passione in un lavoro!"
            </p>
            <p class="text-sm font-semibold text-fucsia-magia">- Alessia, Istruttrice</p>
        </div>
    </div>
</div>

<!-- Coaching Personale -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-compass text-fucsia-magia mr-2"></i>
        Coaching Personale
    </h2>
    <p class="text-gray-600 mb-4">
        Vuoi lavorare sulla tua crescita personale? Offriamo sessioni di coaching individuale per:
    </p>
    <div class="grid md:grid-cols-2 gap-3">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle text-fucsia-magia"></i>
            <span class="text-gray-700">Autostima e fiducia in sé</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle text-fucsia-magia"></i>
            <span class="text-gray-700">Gestione del tempo</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle text-fucsia-magia"></i>
            <span class="text-gray-700">Obiettivi di carriera</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle text-fucsia-magia"></i>
            <span class="text-gray-700">Work-life balance</span>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="btn-fucsia rounded-2xl p-6 text-white text-center">
    <h3 class="text-2xl font-bold mb-2">Vuoi Saperne di Più?</h3>
    <p class="mb-4 text-white text-opacity-90">Contattaci per scoprire tutte le opportunità disponibili</p>
    <a href="{{ route('cliente.profilo') }}" class="inline-block bg-white text-fucsia-magia px-8 py-3 rounded-xl font-bold hover:shadow-xl transition">
        Contattaci Ora
    </a>
</div>

@endsection
