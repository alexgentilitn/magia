<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Nostre Sedi - MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#8B5CF6',
                        'fucsia-magia': '#EC4899',
                    }
                }
            }
        }
    </script>
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- Header -->
    <header class="bg-gradient-to-r from-viola-magia to-fucsia-magia text-white py-16">
        <div class="container mx-auto px-6">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Le Nostre Sedi</h1>
                <p class="text-xl text-purple-100">5 centri benessere distribuiti in tutta Italia</p>
            </div>
        </div>
    </header>

    <!-- Mappa Interattiva -->
    <section class="py-12">
        <div class="container mx-auto px-6">

            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div id="map"></div>
            </div>

        </div>
    </section>

    <!-- Lista Sedi -->
    <section class="py-12">
        <div class="container mx-auto px-6">

            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">Trova la Sede Più Vicina</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Sede 1: Milano -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                    <div class="bg-gradient-to-br from-viola-magia to-fucsia-magia p-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i>
                            Milano Centro
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-location-dot text-viola-magia mt-1"></i>
                                <span>Via Roma 123, 20100 Milano (MI)</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-phone text-viola-magia mt-1"></i>
                                <span>+39 02 1234 5678</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-envelope text-viola-magia mt-1"></i>
                                <span>milano@magiadonna.it</span>
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Orari:</p>
                            <p class="text-sm text-gray-600">Lun-Ven: 08:00 - 20:00</p>
                            <p class="text-sm text-gray-600">Sab: 09:00 - 18:00</p>
                            <p class="text-sm text-gray-600">Dom: Chiuso</p>
                        </div>

                        <button onclick="focusMarker(0)" class="mt-6 w-full px-4 py-3 bg-viola-magia text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            <i class="fas fa-map mr-2"></i>Visualizza sulla Mappa
                        </button>
                    </div>
                </div>

                <!-- Sede 2: Roma -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                    <div class="bg-gradient-to-br from-viola-magia to-fucsia-magia p-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i>
                            Roma Prati
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-location-dot text-viola-magia mt-1"></i>
                                <span>Via Giulia 456, 00192 Roma (RM)</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-phone text-viola-magia mt-1"></i>
                                <span>+39 06 9876 5432</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-envelope text-viola-magia mt-1"></i>
                                <span>roma@magiadonna.it</span>
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Orari:</p>
                            <p class="text-sm text-gray-600">Lun-Ven: 08:00 - 20:00</p>
                            <p class="text-sm text-gray-600">Sab: 09:00 - 18:00</p>
                            <p class="text-sm text-gray-600">Dom: Chiuso</p>
                        </div>

                        <button onclick="focusMarker(1)" class="mt-6 w-full px-4 py-3 bg-viola-magia text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            <i class="fas fa-map mr-2"></i>Visualizza sulla Mappa
                        </button>
                    </div>
                </div>

                <!-- Sede 3: Firenze -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                    <div class="bg-gradient-to-br from-viola-magia to-fucsia-magia p-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i>
                            Firenze Centro
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-location-dot text-viola-magia mt-1"></i>
                                <span>Piazza Duomo 789, 50100 Firenze (FI)</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-phone text-viola-magia mt-1"></i>
                                <span>+39 055 2468 1357</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-envelope text-viola-magia mt-1"></i>
                                <span>firenze@magiadonna.it</span>
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Orari:</p>
                            <p class="text-sm text-gray-600">Lun-Ven: 08:00 - 20:00</p>
                            <p class="text-sm text-gray-600">Sab: 09:00 - 18:00</p>
                            <p class="text-sm text-gray-600">Dom: Chiuso</p>
                        </div>

                        <button onclick="focusMarker(2)" class="mt-6 w-full px-4 py-3 bg-viola-magia text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            <i class="fas fa-map mr-2"></i>Visualizza sulla Mappa
                        </button>
                    </div>
                </div>

                <!-- Sede 4: Napoli -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                    <div class="bg-gradient-to-br from-viola-magia to-fucsia-magia p-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i>
                            Napoli Vomero
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-location-dot text-viola-magia mt-1"></i>
                                <span>Via Toledo 321, 80100 Napoli (NA)</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-phone text-viola-magia mt-1"></i>
                                <span>+39 081 3691 2580</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-envelope text-viola-magia mt-1"></i>
                                <span>napoli@magiadonna.it</span>
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Orari:</p>
                            <p class="text-sm text-gray-600">Lun-Ven: 08:00 - 20:00</p>
                            <p class="text-sm text-gray-600">Sab: 09:00 - 18:00</p>
                            <p class="text-sm text-gray-600">Dom: Chiuso</p>
                        </div>

                        <button onclick="focusMarker(3)" class="mt-6 w-full px-4 py-3 bg-viola-magia text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            <i class="fas fa-map mr-2"></i>Visualizza sulla Mappa
                        </button>
                    </div>
                </div>

                <!-- Sede 5: Torino -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                    <div class="bg-gradient-to-br from-viola-magia to-fucsia-magia p-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i>
                            Torino Crocetta
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-location-dot text-viola-magia mt-1"></i>
                                <span>Corso Vittorio 654, 10100 Torino (TO)</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-phone text-viola-magia mt-1"></i>
                                <span>+39 011 7531 9642</span>
                            </p>
                            <p class="flex items-start gap-3 text-gray-700">
                                <i class="fas fa-envelope text-viola-magia mt-1"></i>
                                <span>torino@magiadonna.it</span>
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Orari:</p>
                            <p class="text-sm text-gray-600">Lun-Ven: 08:00 - 20:00</p>
                            <p class="text-sm text-gray-600">Sab: 09:00 - 18:00</p>
                            <p class="text-sm text-gray-600">Dom: Chiuso</p>
                        </div>

                        <button onclick="focusMarker(4)" class="mt-6 w-full px-4 py-3 bg-viola-magia text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            <i class="fas fa-map mr-2"></i>Visualizza sulla Mappa
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Vuoi Provare Gratuitamente?</h2>
            <p class="text-xl text-purple-100 mb-8">Prenota ora la tua giornata di prova in una delle nostre sedi</p>
            <a href="{{ route('giornata-prova.index') }}" class="inline-block px-8 py-4 bg-white text-viola-magia font-bold text-lg rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                <i class="fas fa-calendar-check mr-2"></i>
                Prenota la Giornata di Prova
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} MA.GIA DONNA. Tutti i diritti riservati.
            </p>
        </div>
    </footer>

    <!-- Google Maps API -->
    <script>
        let map;
        let markers = [];

        // Sedi con coordinate (dati di esempio - sostituire con coordinate reali)
        const sedi = [
            { nome: "Milano Centro", lat: 45.4642, lng: 9.1900 },
            { nome: "Roma Prati", lat: 41.9028, lng: 12.4964 },
            { nome: "Firenze Centro", lat: 43.7696, lng: 11.2558 },
            { nome: "Napoli Vomero", lat: 40.8518, lng: 14.2681 },
            { nome: "Torino Crocetta", lat: 45.0703, lng: 7.6869 }
        ];

        function initMap() {
            // Centro Italia come punto di partenza
            const center = { lat: 42.5, lng: 12.5 };

            // Crea mappa
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: center,
                mapTypeControl: false,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });

            // Aggiungi marker per ogni sede
            sedi.forEach((sede, index) => {
                const marker = new google.maps.Marker({
                    position: { lat: sede.lat, lng: sede.lng },
                    map: map,
                    title: sede.nome,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 24 24'%3E%3Cpath fill='%238B5CF6' d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z'/%3E%3C/svg%3E",
                        scaledSize: new google.maps.Size(40, 40)
                    }
                });

                // Info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div style="padding: 10px;">
                                <h3 style="font-weight: bold; color: #8B5CF6; margin-bottom: 5px;">${sede.nome}</h3>
                                <p style="font-size: 14px; color: #666;">Clicca per maggiori informazioni</p>
                              </div>`
                });

                marker.addListener("click", () => {
                    markers.forEach(m => m.infoWindow.close());
                    infoWindow.open(map, marker);
                    map.panTo(marker.getPosition());
                    map.setZoom(14);
                });

                markers.push({ marker, infoWindow });
            });
        }

        function focusMarker(index) {
            if (markers[index]) {
                map.panTo(markers[index].marker.getPosition());
                map.setZoom(14);
                markers[index].infoWindow.open(map, markers[index].marker);

                // Scroll to map
                document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Load Google Maps API (Sostituire YOUR_API_KEY con una chiave API reale)
        function loadGoogleMaps() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        // Carica mappa al caricamento pagina
        window.addEventListener('load', loadGoogleMaps);
    </script>

</body>
</html>
