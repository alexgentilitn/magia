<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Scopri le sedi MA.GIA DONNA - Trova il centro fitness più vicino a te tra le nostre 5 location">
    <title>Le Nostre Sedi | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        #map {
            width: 100%;
            height: 600px;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            #map {
                height: 400px;
            }
        }

        .location-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.2);
        }

        .location-card.active {
            border-color: #9333ea;
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.05), rgba(236, 72, 153, 0.05));
        }

        /* Custom Google Maps Info Window */
        .gm-style .gm-style-iw-c {
            border-radius: 1rem !important;
            padding: 1rem !important;
        }

        .gm-style .gm-style-iw-d {
            overflow: auto !important;
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
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
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-600 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-4">
                <i class="fas fa-map-marker-alt mr-3"></i>Le Nostre Sedi
            </h1>
            <p class="text-xl text-purple-100 max-w-2xl mx-auto">
                Trova il centro MA.GIA DONNA più vicino a te. {{ $sedi->count() }} location al tuo servizio!
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">

        @if($sedi->count() === 0)
            <!-- Nessuna sede disponibile -->
            <div class="text-center py-20">
                <div class="text-6xl mb-6">📍</div>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Nessuna sede disponibile al momento</h2>
                <p class="text-gray-600 mb-8">Le nostre sedi verranno aggiunte a breve. Torna presto!</p>
                <a href="{{ route('home') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Torna alla Home
                </a>
            </div>
        @else

            <!-- Layout: Mappa + Lista Sedi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Colonna Sinistra: Lista Sedi -->
                <div class="lg:col-span-1 space-y-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-list mr-2 text-purple-600"></i>Tutte le Sedi
                    </h2>

                    @foreach($sedi as $sede)
                        <div class="location-card bg-white rounded-xl p-6 shadow-md border-2 border-transparent"
                             data-location-id="{{ $sede->id }}"
                             onclick="focusOnLocation({{ $sede->id }})">

                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-xl font-bold text-gray-800">{{ $sede->nome }}</h3>
                                @if($sede->sede_principale)
                                    <span class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs px-3 py-1 rounded-full">
                                        Principale
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2 text-sm text-gray-600">
                                <p>
                                    <i class="fas fa-map-marker-alt text-purple-600 w-5"></i>
                                    {{ $sede->indirizzo_completo }}
                                </p>

                                @if($sede->telefono)
                                    <p>
                                        <i class="fas fa-phone text-purple-600 w-5"></i>
                                        <a href="tel:{{ $sede->telefono }}" class="hover:text-purple-600">{{ $sede->telefono }}</a>
                                    </p>
                                @endif

                                @if($sede->email)
                                    <p>
                                        <i class="fas fa-envelope text-purple-600 w-5"></i>
                                        <a href="mailto:{{ $sede->email }}" class="hover:text-purple-600">{{ $sede->email }}</a>
                                    </p>
                                @endif
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ $sede->link_maps }}"
                                   target="_blank"
                                   class="flex-1 bg-purple-100 text-purple-700 text-center px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-200 transition">
                                    <i class="fas fa-directions mr-2"></i>Indicazioni
                                </a>
                                <a href="{{ route('locations.show', $sede->slug) }}"
                                   class="flex-1 bg-pink-100 text-pink-700 text-center px-4 py-2 rounded-lg text-sm font-semibold hover:bg-pink-200 transition">
                                    <i class="fas fa-info-circle mr-2"></i>Dettagli
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Colonna Destra: Mappa Interattiva -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-map text-purple-600 mr-2"></i>Mappa Interattiva
                        </h2>
                        <p class="text-gray-600 mb-6">Clicca sui marker per visualizzare i dettagli di ogni sede</p>

                        <div id="map"></div>

                        <div class="mt-4 text-sm text-gray-500 text-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Utilizza la rotellina del mouse per zoomare sulla mappa
                        </div>
                    </div>
                </div>

            </div>

            <!-- CTA Section -->
            <div class="mt-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-12 text-center text-white shadow-2xl">
                <h2 class="text-3xl font-bold mb-4">Pronta a Iniziare?</h2>
                <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                    Prenota la tua giornata di prova gratuita e scopri il centro più adatto a te!
                </p>
                <a href="{{ route('landing.giornata-prova') }}"
                   class="inline-block bg-white text-purple-600 px-10 py-4 rounded-full text-lg font-bold hover:bg-purple-50 transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-calendar-check mr-2"></i>Prenota la Tua Prova Gratuita
                </a>
            </div>

        @endif

    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} MA.GIA DONNA - Tutti i diritti riservati</p>
        </div>
    </footer>

    <!-- Google Maps JavaScript API -->
    <script>
        // Dati delle location da PHP
        const locations = @json($locations);
        const centerLat = {{ $centerLat }};
        const centerLng = {{ $centerLng }};

        let map;
        let markers = [];
        let infoWindows = [];

        // Inizializza la mappa
        function initMap() {
            // Opzioni mappa con stile personalizzato
            const mapOptions = {
                zoom: locations.length === 1 ? 15 : 10,
                center: { lat: centerLat, lng: centerLng },
                styles: [
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [{ "visibility": "off" }]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{ "color": "#e9d7f7" }]
                    }
                ],
                mapTypeControl: true,
                fullscreenControl: true,
                streetViewControl: false,
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Crea marker per ogni location
            locations.forEach((location, index) => {
                const marker = new google.maps.Marker({
                    position: { lat: location.lat, lng: location.lng },
                    map: map,
                    title: location.nome,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 12,
                        fillColor: '#9333ea',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 3,
                    }
                });

                // Info Window content
                const infoContent = `
                    <div style="max-width: 300px; padding: 10px;">
                        <h3 style="font-size: 18px; font-weight: bold; color: #9333ea; margin-bottom: 10px;">
                            ${location.nome}
                        </h3>
                        <p style="color: #666; margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #9333ea; width: 20px;"></i>
                            ${location.indirizzo}
                        </p>
                        ${location.telefono ? `
                            <p style="color: #666; margin-bottom: 8px;">
                                <i class="fas fa-phone" style="color: #9333ea; width: 20px;"></i>
                                <a href="tel:${location.telefono}" style="color: #9333ea;">${location.telefono}</a>
                            </p>
                        ` : ''}
                        ${location.email ? `
                            <p style="color: #666; margin-bottom: 12px;">
                                <i class="fas fa-envelope" style="color: #9333ea; width: 20px;"></i>
                                <a href="mailto:${location.email}" style="color: #9333ea;">${location.email}</a>
                            </p>
                        ` : ''}
                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                            <a href="https://www.google.com/maps?q=${location.lat},${location.lng}"
                               target="_blank"
                               style="flex: 1; background: #9333ea; color: white; text-align: center; padding: 8px; border-radius: 8px; text-decoration: none; font-size: 14px;">
                                <i class="fas fa-directions"></i> Indicazioni
                            </a>
                            <a href="/locations/${location.slug}"
                               style="flex: 1; background: #ec4899; color: white; text-align: center; padding: 8px; border-radius: 8px; text-decoration: none; font-size: 14px;">
                                <i class="fas fa-info-circle"></i> Dettagli
                            </a>
                        </div>
                    </div>
                `;

                const infoWindow = new google.maps.InfoWindow({
                    content: infoContent
                });

                // Evento click sul marker
                marker.addListener('click', () => {
                    // Chiudi tutte le altre info window
                    infoWindows.forEach(iw => iw.close());

                    // Apri questa info window
                    infoWindow.open(map, marker);

                    // Evidenzia la card corrispondente
                    highlightLocationCard(location.id);
                });

                markers.push(marker);
                infoWindows.push(infoWindow);
            });

            // Auto-fit bounds se ci sono più location
            if (locations.length > 1) {
                const bounds = new google.maps.LatLngBounds();
                locations.forEach(location => {
                    bounds.extend({ lat: location.lat, lng: location.lng });
                });
                map.fitBounds(bounds);
            }
        }

        // Focus su una location specifica (chiamato dal click sulla card)
        function focusOnLocation(locationId) {
            const location = locations.find(l => l.id === locationId);
            const markerIndex = locations.findIndex(l => l.id === locationId);

            if (location && markerIndex !== -1) {
                // Centro la mappa sulla location
                map.setCenter({ lat: location.lat, lng: location.lng });
                map.setZoom(15);

                // Apro l'info window
                infoWindows.forEach(iw => iw.close());
                infoWindows[markerIndex].open(map, markers[markerIndex]);

                // Animazione marker
                markers[markerIndex].setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => {
                    markers[markerIndex].setAnimation(null);
                }, 1400);

                // Evidenzio la card
                highlightLocationCard(locationId);
            }
        }

        // Evidenzia la card di una location
        function highlightLocationCard(locationId) {
            // Rimuovi evidenziazione da tutte le card
            document.querySelectorAll('.location-card').forEach(card => {
                card.classList.remove('active');
            });

            // Aggiungi evidenziazione alla card selezionata
            const card = document.querySelector(`[data-location-id="${locationId}"]`);
            if (card) {
                card.classList.add('active');

                // Scroll smooth alla card su mobile
                if (window.innerWidth < 1024) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }

        // Carica la mappa quando la pagina è pronta
        window.onload = function() {
            // Nota: Questa è una versione di sviluppo
            // In produzione, sostituire con la tua API Key di Google Maps

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        };
    </script>

</body>
</html>
