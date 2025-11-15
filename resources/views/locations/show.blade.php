<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $sede->descrizione ?? 'Scopri la sede ' . $sede->nome . ' di MA.GIA DONNA' }}">
    <title>{{ $sede->nome }} | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        #mapDetail {
            width: 100%;
            height: 400px;
            border-radius: 1rem;
        }

        .info-card {
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    MA.GIA DONNA
                </a>
                <div class="space-x-4">
                    <a href="{{ route('locations.index') }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Tutte le Sedi
                    </a>
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section con Immagine -->
    <div class="relative h-96 bg-gradient-to-r from-purple-600 to-pink-600 overflow-hidden">
        @if($sede->immagine_copertina)
            <img src="{{ asset('storage/' . $sede->immagine_copertina) }}"
                 alt="{{ $sede->nome }}"
                 class="w-full h-full object-cover opacity-30">
        @endif
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h1 class="text-5xl font-bold mb-4">{{ $sede->nome }}</h1>
                @if($sede->sede_principale)
                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full text-lg">
                        <i class="fas fa-star mr-2"></i>Sede Principale
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Colonna Sinistra: Info Principale -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Descrizione -->
                @if($sede->descrizione)
                    <div class="bg-white rounded-xl p-8 shadow-lg">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-purple-600 mr-2"></i>Descrizione
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $sede->descrizione }}</p>
                    </div>
                @endif

                <!-- Mappa Dettaglio -->
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-map text-purple-600 mr-2"></i>Dove Siamo
                    </h2>
                    <div id="mapDetail" class="mb-4"></div>
                    <div class="flex gap-4">
                        <a href="{{ $sede->link_maps }}"
                           target="_blank"
                           class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                            <i class="fas fa-directions mr-2"></i>Apri in Google Maps
                        </a>
                    </div>
                </div>

                <!-- Tipologie Corsi -->
                @if($sede->tipologie_corsi && count($sede->tipologie_corsi) > 0)
                    <div class="bg-white rounded-xl p-8 shadow-lg">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-dumbbell text-purple-600 mr-2"></i>Corsi Disponibili
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($sede->tipologie_corsi as $corso)
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 text-center">
                                    <i class="fas fa-check-circle text-purple-600 mb-2"></i>
                                    <p class="font-semibold text-gray-800">{{ $corso }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Note Accesso -->
                @if($sede->note_accesso)
                    <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6">
                        <h3 class="font-bold text-orange-800 mb-2">
                            <i class="fas fa-exclamation-circle mr-2"></i>Note di Accesso
                        </h3>
                        <p class="text-orange-700">{{ $sede->note_accesso }}</p>
                    </div>
                @endif

            </div>

            <!-- Colonna Destra: Info Rapide -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Indirizzo -->
                <div class="info-card bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>Indirizzo
                    </h3>
                    <p class="text-gray-700">{{ $sede->indirizzo_completo }}</p>
                </div>

                <!-- Contatti -->
                <div class="info-card bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-phone text-purple-600 mr-2"></i>Contatti
                    </h3>
                    <div class="space-y-3">
                        @if($sede->telefono)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Telefono</p>
                                <a href="tel:{{ $sede->telefono }}" class="text-purple-600 font-semibold hover:text-purple-800">
                                    {{ $sede->telefono }}
                                </a>
                            </div>
                        @endif

                        @if($sede->email)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <a href="mailto:{{ $sede->email }}" class="text-purple-600 font-semibold hover:text-purple-800 break-all">
                                    {{ $sede->email }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Orari Apertura -->
                @if($sede->orari_apertura)
                    <div class="info-card bg-white rounded-xl p-6 shadow-lg">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-clock text-purple-600 mr-2"></i>Orari di Apertura
                        </h3>
                        <div class="space-y-2 text-sm">
                            @php
                                $giorni = [
                                    'lunedi' => 'Lunedì',
                                    'martedi' => 'Martedì',
                                    'mercoledi' => 'Mercoledì',
                                    'giovedi' => 'Giovedì',
                                    'venerdi' => 'Venerdì',
                                    'sabato' => 'Sabato',
                                    'domenica' => 'Domenica',
                                ];
                            @endphp

                            @foreach($giorni as $key => $giorno)
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-semibold text-gray-700">{{ $giorno }}</span>
                                    <span class="text-gray-600">
                                        {{ $sede->orari_apertura[$key] ?? 'Chiuso' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Capienza -->
                @if($sede->capienza_massima)
                    <div class="info-card bg-white rounded-xl p-6 shadow-lg">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            <i class="fas fa-users text-purple-600 mr-2"></i>Capienza Massima
                        </h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $sede->capienza_massima }}</p>
                        <p class="text-sm text-gray-500 mt-1">persone per lezione</p>
                    </div>
                @endif

                <!-- CTA Prenota -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-6 text-white text-center shadow-xl">
                    <h3 class="text-xl font-bold mb-2">Pronta a Iniziare?</h3>
                    <p class="text-purple-100 mb-4 text-sm">Prenota la tua giornata di prova gratuita!</p>
                    <a href="{{ route('landing.giornata-prova') }}"
                       class="block bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition transform hover:scale-105">
                        <i class="fas fa-calendar-check mr-2"></i>Prenota Ora
                    </a>
                </div>

            </div>

        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} MA.GIA DONNA - Tutti i diritti riservati</p>
        </div>
    </footer>

    <!-- Google Maps Script -->
    <script>
        const sede = @json([
            'nome' => $sede->nome,
            'lat' => (float) $sede->latitudine,
            'lng' => (float) $sede->longitudine,
            'indirizzo' => $sede->indirizzo_completo,
        ]);

        function initMap() {
            const location = { lat: sede.lat, lng: sede.lng };

            const map = new google.maps.Map(document.getElementById('mapDetail'), {
                zoom: 15,
                center: location,
                styles: [
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [{ "visibility": "off" }]
                    }
                ]
            });

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: sede.nome,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 15,
                    fillColor: '#9333ea',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 4,
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h3 style="font-weight: bold; color: #9333ea; margin-bottom: 8px;">${sede.nome}</h3>
                        <p style="color: #666;">${sede.indirizzo}</p>
                    </div>
                `
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            // Auto-open info window
            infoWindow.open(map, marker);
        }

        window.onload = function() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        };
    </script>

</body>
</html>
