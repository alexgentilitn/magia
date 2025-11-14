@extends('layouts.pubblico')

@section('titolo', 'Benessere al Femminile')

@section('contenuto')

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-viola-magia to-fucsia-magia text-white py-20 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Benessere, Energia e Bellezza<br>al Femminile
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-pink-100">
                La tua energia, la nostra missione
            </p>
            <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto">
                Entra anche tu in questo progetto di rinascita attraverso il movimento del corpo, la musica e il divertimento di Balla & Snella
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('registrazione') }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-white text-fucsia-magia font-bold rounded-full hover:bg-pink-50 transition transform hover:scale-105 shadow-xl text-lg">
                    <i class="fas fa-star mr-2"></i>
                    Iscriviti Ora
                </a>
                <a href="#programmi" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-fucsia-magia transition transform hover:scale-105 text-lg">
                    <i class="fas fa-arrow-down mr-2"></i>
                    Scopri i Programmi
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Sezione Intro -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                Il Progetto MA.GIA DONNA
            </h2>
            <p class="text-lg text-gray-600 mb-4">
                MA.GIA è nata con l'idea di offrire un servizio alla donna a 360 gradi per il loro benessere, del corpo e della mente.
            </p>
            <p class="text-lg text-gray-600">
                Attraverso il movimento del corpo, la musica e il divertimento di <strong class="text-fucsia-magia">Balla & Snella</strong>, accompagniamo le donne in un viaggio di trasformazione non solo fisica ma anche emotiva.
            </p>
        </div>
    </div>
</section>

<!-- Sezione Programmi (Placeholder) -->
<section id="programmi" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                I Nostri Programmi
            </h2>
            <p class="text-lg text-gray-600">
                Scegli il percorso più adatto a te
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            
            <!-- Balla & Snella -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition">
                <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia h-48 flex items-center justify-center">
                    <i class="fas fa-music text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Balla & Snella</h3>
                    <p class="text-gray-600 mb-4">
                        Movimento, musica e divertimento per trasformare il tuo corpo e la tua mente.
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-fucsia-magia mr-2"></i>
                            Lezioni di gruppo dinamiche
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-fucsia-magia mr-2"></i>
                            Piano alimentare personalizzato
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-fucsia-magia mr-2"></i>
                            Monitoraggio parametri corporei
                        </li>
                    </ul>
                    <a href="{{ route('registrazione') }}" 
                       class="block text-center py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-bold rounded-lg hover:shadow-lg transition">
                        Iscriviti Ora
                    </a>
                </div>
            </div>

            <!-- Programma Personalizzato -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition">
                <div class="bg-gradient-to-r from-arancio-magia to-fucsia-magia h-48 flex items-center justify-center">
                    <i class="fas fa-heart text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Offerta MA.GIA</h3>
                    <p class="text-gray-600 mb-4">
                        Percorso completamente personalizzato su misura per te.
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-arancio-magia mr-2"></i>
                            Consulenza nutrizionale dedicata
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-arancio-magia mr-2"></i>
                            Trattamenti estetici personalizzati
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-arancio-magia mr-2"></i>
                            Supporto continuo
                        </li>
                    </ul>
                    <a href="{{ route('registrazione') }}" 
                       class="block text-center py-3 bg-gradient-to-r from-arancio-magia to-fucsia-magia text-white font-bold rounded-lg hover:shadow-lg transition">
                        Iscriviti Ora
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Sezione Come Funziona -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Come Funziona
            </h2>
            <p class="text-lg text-gray-600">
                Il tuo percorso in 4 semplici step
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-6xl mx-auto">
            
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold">
                    1
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Registrati</h3>
                <p class="text-gray-600">Compila il form con i tuoi dati e scegli il programma</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold">
                    2
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Attivazione</h3>
                <p class="text-gray-600">Dopo il pagamento ricevi l'email di attivazione</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold">
                    3
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Il Tuo Programma</h3>
                <p class="text-gray-600">Accedi alla tua area personale con tutti i materiali</p>
            </div>

            <!-- Step 4 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold">
                    4
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Inizia il Percorso</h3>
                <p class="text-gray-600">Prenota le tue lezioni e inizia a trasformarti!</p>
            </div>

        </div>
    </div>
</section>

<!-- CTA Finale -->
<section class="py-16 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">
            Pronta a Iniziare?
        </h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Unisciti alle centinaia di donne che hanno già iniziato il loro percorso di trasformazione
        </p>
        <a href="{{ route('registrazione') }}" 
           class="inline-flex items-center px-10 py-5 bg-white text-fucsia-magia font-bold rounded-full hover:bg-pink-50 transition transform hover:scale-105 shadow-2xl text-xl">
            <i class="fas fa-rocket mr-3"></i>
            Iscriviti Gratuitamente
        </a>
    </div>
</section>

@endsection
