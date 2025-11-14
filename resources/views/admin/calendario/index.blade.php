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
        editable: false,
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
});
</script>
@endpush
