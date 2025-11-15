@extends('layouts.admin')

@section('titolo', 'Calendario Lezioni')

@section('contenuto')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-alt text-fucsia-magia mr-2"></i>
                Calendario Lezioni
            </h1>
            <p class="text-gray-600">Visualizza e gestisci tutte le lezioni programmate</p>
        </div>

        <div>
            <a href="{{ route('admin.lezioni.create') }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Nuova Lezione
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Filtri e Legenda (Sidebar) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Filtri -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-filter mr-2 text-fucsia-magia"></i>
                    Filtri
                </h2>

                <div class="space-y-4">
                    <!-- Professionista -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Professionista</label>
                        <select id="filterProfessionista" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent text-sm">
                            <option value="">Tutti</option>
                            @foreach($professionisti as $prof)
                                <option value="{{ $prof->utente_id }}">{{ $prof->nome }} {{ $prof->cognome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sede -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                        <select id="filterSede" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent text-sm">
                            <option value="">Tutte</option>
                            @foreach($sedi as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipologia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipologia</label>
                        <select id="filterTipologia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent text-sm">
                            <option value="">Tutte</option>
                            <option value="gruppo">Gruppo</option>
                            <option value="individuale">Individuale</option>
                            <option value="online">Online</option>
                            <option value="ibrida">Ibrida</option>
                        </select>
                    </div>

                    <!-- Stato -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                        <select id="filterStato" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent text-sm">
                            <option value="">Tutti</option>
                            <option value="programmata">Programmata</option>
                            <option value="confermata">Confermata</option>
                            <option value="in_corso">In Corso</option>
                            <option value="completata">Completata</option>
                            <option value="cancellata">Cancellata</option>
                            <option value="rinviata">Rinviata</option>
                        </select>
                    </div>

                    <!-- Reset Filtri -->
                    <button id="resetFilters" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                        <i class="fas fa-redo mr-2"></i>
                        Reset Filtri
                    </button>
                </div>
            </div>

            <!-- Legenda Tipologie -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-palette mr-2 text-fucsia-magia"></i>
                    Legenda Tipologie
                </h2>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded" style="background-color: #9c27b0;"></div>
                        <span class="ml-2 text-gray-700">Gruppo</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded" style="background-color: #e91e63;"></div>
                        <span class="ml-2 text-gray-700">Individuale</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded" style="background-color: #2196f3;"></div>
                        <span class="ml-2 text-gray-700">Online</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded" style="background-color: #ff9800;"></div>
                        <span class="ml-2 text-gray-700">Ibrida</span>
                    </div>
                </div>
            </div>

            <!-- Legenda Stati (bordi) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-fucsia-magia"></i>
                    Legenda Stati (Bordi)
                </h2>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded border-2" style="border-color: #ffa726;"></div>
                        <span class="ml-2 text-gray-700">Programmata</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded border-2" style="border-color: #66bb6a;"></div>
                        <span class="ml-2 text-gray-700">Confermata</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded border-2" style="border-color: #42a5f5;"></div>
                        <span class="ml-2 text-gray-700">In Corso</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded border-2" style="border-color: #26a69a;"></div>
                        <span class="ml-2 text-gray-700">Completata</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded border-2" style="border-color: #ef5350;"></div>
                        <span class="ml-2 text-gray-700">Cancellata</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-6">
                <div id="calendario"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dettagli Lezione -->
<div id="modalDettagli" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800" id="modalTitolo"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="modalContenuto" class="mt-4">
            <!-- Contenuto caricato dinamicamente -->
        </div>
    </div>
</div>

<!-- Modal Crea Lezione -->
<div id="modalCreaLezione" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-fucsia-magia mr-2"></i>
                Crea Nuova Lezione
            </h3>
            <button onclick="closeModalCrea()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="modalContenutoCrea" class="mt-4">
            @include('admin.calendario.partials.modal-crea-lezione')
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- FullCalendar CSS e JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/it.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendario');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'it',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Oggi',
            month: 'Mese',
            week: 'Settimana',
            day: 'Giorno',
            list: 'Lista'
        },
        height: 'auto',
        navLinks: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekNumbers: true,
        weekText: 'Sett.',
        allDaySlot: false,
        slotMinTime: '06:00:00',
        slotMaxTime: '23:00:00',
        slotDuration: '00:30:00',

        // Click su una data per creare nuova lezione
        dateClick: function(info) {
            apriModalCreaLezione(info.dateStr);
        },

        // Gestione drag & drop
        eventDrop: function(info) {
            const lezioneId = info.event.id;
            const newStart = info.event.start;
            const newEnd = info.event.end;

            // Mostra loading
            Swal.fire({
                title: 'Spostamento in corso...',
                text: 'Verifica conflitti di orario...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('admin/calendario') }}/${lezioneId}/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    start: newStart.toISOString(),
                    end: newEnd.toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lezione spostata!',
                        html: `Nuova data: <strong>${data.lezione.data}</strong><br>Orario: <strong>${data.lezione.ora_inizio} - ${data.lezione.ora_fine}</strong>`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    // Errore - ripristina posizione originale
                    info.revert();
                    Swal.fire({
                        icon: 'error',
                        title: 'Impossibile spostare',
                        text: data.message || 'Errore durante lo spostamento'
                    });
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                info.revert();
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Si è verificato un errore durante lo spostamento'
                });
            });
        },

        // Gestione resize (modifica durata)
        eventResize: function(info) {
            const lezioneId = info.event.id;
            const newStart = info.event.start;
            const newEnd = info.event.end;

            // Calcola nuova durata
            const durataMinuti = Math.round((newEnd - newStart) / 1000 / 60);

            // Mostra loading
            Swal.fire({
                title: 'Modifica durata...',
                text: `Nuova durata: ${durataMinuti} minuti`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('admin/calendario') }}/${lezioneId}/resize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    start: newStart.toISOString(),
                    end: newEnd.toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Durata modificata!',
                        html: `Orario: <strong>${data.lezione.ora_inizio} - ${data.lezione.ora_fine}</strong><br>Durata: <strong>${data.lezione.durata_minuti} minuti</strong>`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    // Errore - ripristina durata originale
                    info.revert();
                    Swal.fire({
                        icon: 'error',
                        title: 'Impossibile modificare',
                        text: data.message || 'Errore durante la modifica della durata'
                    });
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                info.revert();
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Si è verificato un errore durante la modifica'
                });
            });
        },

        // Carica eventi dal server
        events: function(info, successCallback, failureCallback) {
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr,
                professionista_id: document.getElementById('filterProfessionista').value,
                sede_id: document.getElementById('filterSede').value,
                tipologia: document.getElementById('filterTipologia').value,
                stato: document.getElementById('filterStato').value,
            });

            fetch('{{ route("admin.calendario.events") }}?' + params.toString())
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },

        // Click su evento
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            mostraDettagli(info.event.id);
        },

        // Renderizzazione custom eventi per mostrare più informazioni
        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            const title = arg.event.title;
            const professionista = props.professionista || 'Non assegnato';
            const posti = props.posti || '';

            // Crea contenitore principale
            const container = document.createElement('div');
            container.className = 'fc-event-main-frame p-1';
            container.style.fontSize = '0.75rem'; // text-xs
            container.style.lineHeight = '1.1';

            // Titolo lezione
            const titleDiv = document.createElement('div');
            titleDiv.className = 'font-semibold truncate';
            titleDiv.textContent = title;
            titleDiv.style.fontSize = '0.8rem';
            container.appendChild(titleDiv);

            // Nome istruttore (più piccolo)
            const profDiv = document.createElement('div');
            profDiv.className = 'text-white/90 truncate mt-0.5';
            profDiv.innerHTML = `<i class="fas fa-user-tie mr-1" style="font-size: 0.65rem;"></i><span style="font-size: 0.7rem;">${professionista}</span>`;
            container.appendChild(profDiv);

            // Posti (solo per lezioni di gruppo)
            if (posti && props.tipologia === 'gruppo') {
                const postiDiv = document.createElement('div');
                postiDiv.className = 'text-white/80 truncate mt-0.5';
                postiDiv.innerHTML = `<i class="fas fa-users mr-1" style="font-size: 0.65rem;"></i><span style="font-size: 0.65rem;">${posti}</span>`;
                container.appendChild(postiDiv);
            }

            return { domNodes: [container] };
        },

        // Tooltip al passaggio mouse
        eventMouseEnter: function(info) {
            const props = info.event.extendedProps;
            const tooltip = `
                <div class="absolute z-10 bg-gray-900 text-white text-sm rounded-lg p-3 shadow-lg" style="max-width: 300px;">
                    <p class="font-bold mb-1">${info.event.title}</p>
                    <p><i class="fas fa-user-tie mr-1"></i> ${props.professionista}</p>
                    <p><i class="fas fa-map-marker-alt mr-1"></i> ${props.sede}</p>
                    <p><i class="fas fa-users mr-1"></i> Posti: ${props.posti}</p>
                    <p class="mt-2 text-xs">${props.descrizione || 'Nessuna descrizione'}</p>
                </div>
            `;
            // Implementare tooltip se necessario
        }
    });

    calendar.render();

    // Gestione filtri
    const filters = ['filterProfessionista', 'filterSede', 'filterTipologia', 'filterStato'];
    filters.forEach(filterId => {
        document.getElementById(filterId).addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });

    // Reset filtri
    document.getElementById('resetFilters').addEventListener('click', function() {
        filters.forEach(filterId => {
            document.getElementById(filterId).value = '';
        });
        calendar.refetchEvents();
    });

    // Funzione globale per mostrare dettagli
    window.mostraDettagli = function(lezioneId) {
        fetch(`{{ url('admin/calendario') }}/${lezioneId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitolo').textContent = data.lezione.titolo;
                document.getElementById('modalContenuto').innerHTML = data.html;
                document.getElementById('modalDettagli').classList.remove('hidden');
            })
            .catch(error => console.error('Errore:', error));
    };

    // Funzione globale per chiudere modal
    window.closeModal = function() {
        document.getElementById('modalDettagli').classList.add('hidden');
    };

    // Chiudi modal cliccando fuori
    document.getElementById('modalDettagli').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Funzione globale per aprire modal creazione lezione
    window.apriModalCreaLezione = function(data) {
        // Pre-compila la data
        document.getElementById('data').value = data;

        // Resetta il form
        document.getElementById('formCreaLezione').reset();
        document.getElementById('data').value = data; // Riapplica dopo reset

        // Nascondi eventuali errori precedenti
        document.getElementById('erroreCreaLezione').classList.add('hidden');

        // Mostra il modal
        document.getElementById('modalCreaLezione').classList.remove('hidden');
    };

    // Funzione globale per chiudere modal creazione
    window.closeModalCrea = function() {
        document.getElementById('modalCreaLezione').classList.add('hidden');
        document.getElementById('formCreaLezione').reset();
        document.getElementById('erroreCreaLezione').classList.add('hidden');
    };

    // Gestione submit form creazione lezione
    document.getElementById('formCreaLezione').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Mostra loading sul pulsante
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creazione...';
        submitBtn.disabled = true;

        fetch('{{ route("admin.lezioni.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || formData.get('_token'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    return Promise.reject(data);
                }
                return data;
            });
        })
        .then(data => {
            // Successo - chiudi modal e ricarica calendario
            closeModalCrea();
            calendar.refetchEvents();

            // Mostra notifica successo con SweetAlert2
            Swal.fire({
                icon: 'success',
                title: 'Lezione creata!',
                text: data.message || 'La lezione è stata creata con successo.',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Errore completo:', error);

            // Mostra errore
            const errorDiv = document.getElementById('erroreCreaLezione');
            const messageEl = document.getElementById('messaggioErrore');

            if (error.message) {
                messageEl.textContent = error.message;
            } else if (error.errors) {
                // Errori di validazione Laravel
                const errori = Object.values(error.errors).flat();
                messageEl.innerHTML = errori.join('<br>');
            } else {
                messageEl.textContent = 'Si è verificato un errore durante la creazione della lezione.';
            }

            errorDiv.classList.remove('hidden');

            // Scroll verso l'errore
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .finally(() => {
            // Ripristina pulsante
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Chiudi modal creazione cliccando fuori
    document.getElementById('modalCreaLezione').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModalCrea();
        }
    });

    // ===================================================================
    // GESTIONE PRENOTAZIONI
    // ===================================================================

    // Toggle form aggiungi partecipante
    window.toggleAggiungiPartecipante = function() {
        const form = document.getElementById('formAggiungiPartecipante');
        form.classList.toggle('hidden');

        // Reset form quando si chiude
        if (form.classList.contains('hidden')) {
            document.getElementById('formPrenota').reset();
        }
    };

    // Prenota cliente a lezione
    window.prenotaCliente = function(event, lezioneId) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const clienteId = formData.get('cliente_id');

        if (!clienteId) {
            Swal.fire({
                icon: 'warning',
                title: 'Attenzione',
                text: 'Seleziona un cliente da prenotare'
            });
            return;
        }

        // Mostra loading
        Swal.fire({
            title: 'Prenotazione in corso...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`{{ url('admin/calendario') }}/${lezioneId}/prenota`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData.get('_token'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                cliente_id: clienteId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Successo
                Swal.fire({
                    icon: 'success',
                    title: data.lista_attesa ? 'Aggiunto alla lista d\'attesa' : 'Prenotazione confermata!',
                    text: data.message,
                    timer: 2000
                });

                // Ricarica modal dettagli
                mostraDettagli(lezioneId);

                // Ricarica eventi calendario
                calendar.refetchEvents();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: data.message || 'Errore durante la prenotazione'
                });
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            Swal.fire({
                icon: 'error',
                title: 'Errore',
                text: 'Si è verificato un errore durante la prenotazione'
            });
        });
    };

    // Annulla prenotazione
    window.annullaPrenotazione = function(lezioneId, clienteId, nomeCliente) {
        Swal.fire({
            title: 'Conferma annullamento',
            html: `Vuoi annullare la prenotazione di<br><strong>${nomeCliente}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sì, annulla',
            cancelButtonText: 'No, mantieni'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostra loading
                Swal.fire({
                    title: 'Annullamento in corso...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`{{ url('admin/calendario') }}/${lezioneId}/prenotazioni/${clienteId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let message = data.message;

                        // Se c'è stato un promosso dalla lista d'attesa
                        if (data.promosso) {
                            message += `<br><br><i class="fas fa-info-circle text-blue-500"></i> <strong>${data.promosso.nome}</strong> è stato promosso dalla lista d'attesa.`;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Prenotazione annullata',
                            html: message,
                            timer: 3000
                        });

                        // Ricarica modal dettagli
                        mostraDettagli(lezioneId);

                        // Ricarica eventi calendario
                        calendar.refetchEvents();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: data.message || 'Errore durante l\'annullamento'
                        });
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Si è verificato un errore durante l\'annullamento'
                    });
                });
            }
        });
    };

    // ===================================================================
    // ELIMINA LEZIONE
    // ===================================================================

    window.eliminaLezione = function(lezioneId, titoloLezione) {
        Swal.fire({
            title: 'Conferma eliminazione',
            html: `Vuoi davvero eliminare la lezione<br><strong>${titoloLezione}</strong>?<br><br>` +
                  `<span class="text-red-600 text-sm">⚠️ Questa azione eliminerà anche tutte le prenotazioni associate!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sì, elimina',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostra loading
                Swal.fire({
                    title: 'Eliminazione in corso...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`{{ url('admin/calendario') }}/${lezioneId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Lezione eliminata',
                            text: data.message || 'La lezione è stata eliminata con successo.',
                            timer: 2000
                        });

                        // Chiudi modal
                        closeModal();

                        // Ricarica eventi calendario
                        calendar.refetchEvents();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: data.message || 'Errore durante l\'eliminazione'
                        });
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Si è verificato un errore durante l\'eliminazione'
                    });
                });
            }
        });
    };
});
</script>
@endpush
