@extends('layouts.cliente')

@section('titolo', 'Alimentazione Smart')

@section('contenuto')

<!-- Back Button -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Sezione -->
<div class="btn-fucsia rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">🥗</div>
    <h1 class="text-4xl font-bold mb-2">Alimentazione Smart</h1>
    <p class="text-xl text-white text-opacity-90">Piani Alimentari Equilibrati</p>
</div>

<!-- Descrizione Programma -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
        Il Nostro Approccio Nutrizionale
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
        Niente diete drastiche o privazioni impossibili da sostenere! Il nostro metodo si basa su un'alimentazione 
        equilibrata, personalizzata e sostenibile nel tempo.
    </p>
    <p class="text-gray-600 leading-relaxed">
        Lavoriamo con professionisti qualificati per creare piani alimentari su misura per te, 
        che tengono conto delle tue esigenze, gusti e stile di vita.
    </p>
</div>

<!-- Servizi Inclusi -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-check-circle text-fucsia-magia mr-2"></i>
        Cosa Include
    </h2>
    <div class="space-y-3">
        <div class="flex items-start space-x-3">
            <i class="fas fa-check text-fucsia-magia mt-1"></i>
            <div>
                <h3 class="font-semibold text-gray-800">Piano Alimentare Personalizzato</h3>
                <p class="text-sm text-gray-600">Creato su misura per te e i tuoi obiettivi</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <i class="fas fa-check text-fucsia-magia mt-1"></i>
            <div>
                <h3 class="font-semibold text-gray-800">Analisi Impedenziometrica</h3>
                <p class="text-sm text-gray-600">Monitoriamo la tua composizione corporea con precisione</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <i class="fas fa-check text-fucsia-magia mt-1"></i>
            <div>
                <h3 class="font-semibold text-gray-800">Ricette e Menu Settimanali</h3>
                <p class="text-sm text-gray-600">Idee pratiche e gustose per ogni giorno</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <i class="fas fa-check text-fucsia-magia mt-1"></i>
            <div>
                <h3 class="font-semibold text-gray-800">Supporto Continuo</h3>
                <p class="text-sm text-gray-600">Consulenze periodiche e aggiustamenti del piano</p>
            </div>
        </div>
    </div>
</div>

<!-- Bilancia Impedenziometrica -->
<div class="bg-gradient-to-r from-fucsia-magia to-viola-magia rounded-2xl p-6 text-white mb-6">
    <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
            <i class="fas fa-weight text-6xl"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold mb-2">Bilancia Impedenziometrica</h3>
            <p class="text-white text-opacity-90">
                Misuriamo massa grassa, massa magra, acqua corporea e metabolismo basale 
                per monitorare i tuoi progressi in modo scientifico.
            </p>
        </div>
    </div>
</div>

<!-- Benefici -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-heart text-fucsia-magia mr-2"></i>
        I Benefici
    </h2>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-fucsia-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-fucsia-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Risultati Duraturi</h3>
                <p class="text-sm text-gray-600">Perdi peso e mantienilo nel tempo</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-fucsia-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-battery-full text-fucsia-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Più Energia</h3>
                <p class="text-sm text-gray-600">Sentiti vitale e piena di forze</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-fucsia-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-brain text-fucsia-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Educazione Alimentare</h3>
                <p class="text-sm text-gray-600">Impara a nutrirti correttamente</p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-10 h-10 bg-fucsia-magia bg-opacity-10 rounded-lg flex items-center justify-center">
                <i class="fas fa-leaf text-fucsia-magia"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Benessere Globale</h3>
                <p class="text-sm text-gray-600">Migliora pelle, capelli e salute</p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonianze -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-quote-left text-fucsia-magia mr-2"></i>
        Storie di Successo
    </h2>
    <div class="space-y-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"Ho imparato a mangiare bene senza rinunce. -12 kg in 4 mesi!"</p>
            <p class="text-sm font-semibold text-fucsia-magia">- Francesca, 42 anni</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-gray-600 italic mb-2">"Finalmente ho un rapporto sano con il cibo."</p>
            <p class="text-sm font-semibold text-fucsia-magia">- Giulia, 35 anni</p>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="btn-fucsia rounded-2xl p-6 text-white text-center">
    <h3 class="text-2xl font-bold mb-2">Inizia il Tuo Percorso</h3>
    <p class="mb-4 text-white text-opacity-90">Prenota la tua prima consulenza nutrizionale!</p>
    <a href="{{ route('cliente.profilo') }}" class="inline-block bg-white text-fucsia-magia px-8 py-3 rounded-xl font-bold hover:shadow-xl transition">
        Richiedi Consulenza
    </a>
</div>

@endsection
