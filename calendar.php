<?php
$pageTitle = 'Calendario';
require_once 'config.php';
requireLogin();

$currentUser = getCurrentUser();

// Verifica se Google Calendar è configurato
$google_calendar_enabled = false;
$google_refresh_token = null;

$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', $currentUser['id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$user_data = $result->fetchArray(SQLITE3_ASSOC);

if (!empty($user_data['google_refresh_token'])) {
    $google_calendar_enabled = true;
    $google_refresh_token = $user_data['google_refresh_token'];
}

include 'includes_header.php';
?>

<style>
/* FullCalendar Custom Styles */
#calendar {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: 1px solid var(--gray-200);
}

.fc {
    font-family: var(--font-family);
}

.fc-toolbar-title {
    font-size: 1.5em !important;
    color: var(--gray-900);
    font-weight: 600;
}

.fc-button {
    background: var(--accent) !important;
    border-color: var(--accent) !important;
    text-transform: capitalize !important;
    font-weight: 500 !important;
    padding: 8px 15px !important;
    transition: all 0.2s !important;
}

.fc-button:hover {
    background: #2980b9 !important;
    border-color: #2980b9 !important;
}

.fc-button-active {
    background: #2980b9 !important;
}

.fc-daygrid-day-number {
    font-weight: 600;
    color: var(--gray-700);
}

.fc-daygrid-day.fc-day-today {
    background: #e3f2fd !important;
}

.fc-event {
    border: none !important;
    border-left: 3px solid !important;
    cursor: pointer !important;
}

.fc-event-title {
    font-weight: 500;
}

/* Colori eventi */
.fc-event.scadenza-in_attesa {
    background: #fff3e0 !important;
    border-left-color: #f57c00 !important;
    color: #f57c00 !important;
}

.fc-event.scadenza-pagato {
    background: #e8f5e9 !important;
    border-left-color: #388e3c !important;
    color: #388e3c !important;
}

.fc-event.google-event {
    background: #e3f2fd !important;
    border-left-color: #1976d2 !important;
    color: #1976d2 !important;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--gray-200);
}

.modal-close {
    font-size: 1.5em;
    cursor: pointer;
    color: var(--gray-500);
    background: none;
    border: none;
}

.modal-close:hover {
    color: var(--gray-900);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--gray-700);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    font-family: var(--font-family);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.sync-banner {
    background: #e3f2fd;
    border-left: 3px solid #1976d2;
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sync-banner.disabled {
    background: #fff3e0;
    border-left-color: #f57c00;
}

/* Selezione colore */
.color-option {
    transition: all 0.2s;
}

input[type="radio"]:checked + .color-option {
    border-color: var(--gray-900) !important;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
}

.color-option:hover {
    transform: scale(1.1);
}
</style>

<!-- Banner Sincronizzazione Google -->
<?php if ($google_calendar_enabled): ?>
    <div class="sync-banner">
        <div>
            <strong><i class="fab fa-google"></i> Google Calendar Connesso</strong>
            <p style="margin: 5px 0 0 0; font-size: 0.9em; color: var(--gray-700);">
                Le scadenze sono sincronizzate automaticamente con il tuo Google Calendar
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="syncNow()" class="btn">
                <i class="fas fa-sync"></i> Sincronizza Ora
            </button>
            <a href="google_calendar_setup.php" class="btn btn-secondary">
                <i class="fas fa-cog"></i> Gestisci
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="sync-banner disabled">
        <div>
            <strong><i class="fab fa-google"></i> Google Calendar non connesso</strong>
            <p style="margin: 5px 0 0 0; font-size: 0.9em; color: var(--gray-700);">
                Collega il tuo account Google per sincronizzare automaticamente le scadenze
            </p>
        </div>
        <a href="google_calendar_setup.php" class="btn btn-success">
            <i class="fas fa-plug"></i> Connetti Google Calendar
        </a>
    </div>
<?php endif; ?>

<!-- Legenda -->
<div class="card" style="margin-bottom: 20px;">
    <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
        <strong style="color: var(--gray-700);">Legenda Eventi:</strong>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: #fff3e0; border-left: 3px solid #f57c00; border-radius: 4px;"></div>
            <span>Scadenza in Attesa</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: #e8f5e9; border-left: 3px solid #388e3c; border-radius: 4px;"></div>
            <span>Scadenza Pagata</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: #9c27b0; border-radius: 4px;"></div>
            <span>Eventi Personali</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: #e3f2fd; border-left: 3px solid #1976d2; border-radius: 4px;"></div>
            <span>Evento Google</span>
        </div>
    </div>
    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--gray-200); font-size: 0.9em; color: var(--gray-600);">
        <strong>Colori disponibili per eventi personali:</strong>
        Viola, Rosa, Arancio, Verde, Blu, Grigio
    </div>
</div>

<!-- Calendario -->
<div id="calendar"></div>

<!-- Modal Dettaglio Evento -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Dettaglio Evento</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
    </div>
</div>

<!-- Modal Nuovo Evento -->
<div id="newEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalNewEventTitle">Nuovo Evento</h3>
            <button class="modal-close" onclick="closeNewEventModal()">&times;</button>
        </div>
        <form id="newEventForm">
            <!-- Scelta tipo evento -->
            <div class="form-group">
                <label>Tipo Evento *</label>
                <select name="event_type" id="eventTypeSelect" onchange="toggleEventForm()" required>
                    <option value="generic">📝 Nota / Appuntamento / Promemoria</option>
                    <option value="scadenza">💰 Scadenza Progetto</option>
                </select>
            </div>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--gray-200);">

            <!-- Form Evento Generico -->
            <div id="genericEventForm">
                <div class="form-group">
                    <label>Titolo *</label>
                    <input type="text" name="titolo" id="titoloGeneric" placeholder="es. Riunione con cliente, Promemoria chiamata, ecc.">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo_evento">
                        <option value="nota">📝 Nota</option>
                        <option value="appuntamento">📅 Appuntamento</option>
                        <option value="promemoria">⏰ Promemoria</option>
                        <option value="riunione">👥 Riunione</option>
                        <option value="telefonata">📞 Telefonata</option>
                        <option value="email">📧 Email da inviare</option>
                        <option value="altro">🔖 Altro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Data/Ora Inizio *</label>
                    <input type="datetime-local" name="data_inizio_generic" id="dataInizioGeneric">
                </div>
                <div class="form-group">
                    <label>Data/Ora Fine</label>
                    <input type="datetime-local" name="data_fine">
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tutto_il_giorno" id="tuttoIlGiorno" checked
                               onchange="toggleTimeFields()"
                               style="width: 18px; height: 18px;">
                        <span>Tutto il giorno</span>
                    </label>
                </div>
                <div class="form-group">
                    <label>Colore Evento</label>
                    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;">
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#9c27b0" checked style="display: none;">
                            <div style="width: 40px; height: 40px; background: #9c27b0; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Viola</small>
                        </label>
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#e91e63" style="display: none;">
                            <div style="width: 40px; height: 40px; background: #e91e63; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Rosa</small>
                        </label>
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#ff9800" style="display: none;">
                            <div style="width: 40px; height: 40px; background: #ff9800; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Arancio</small>
                        </label>
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#4caf50" style="display: none;">
                            <div style="width: 40px; height: 40px; background: #4caf50; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Verde</small>
                        </label>
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#2196f3" style="display: none;">
                            <div style="width: 40px; height: 40px; background: #2196f3; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Blu</small>
                        </label>
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="colore" value="#607d8b" style="display: none;">
                            <div style="width: 40px; height: 40px; background: #607d8b; border-radius: 50%; margin: 0 auto; border: 3px solid transparent;" class="color-option"></div>
                            <small>Grigio</small>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descrizione</label>
                    <textarea name="descrizione_generic" rows="3"></textarea>
                </div>
            </div>

            <!-- Form Scadenza Progetto -->
            <div id="scadenzaEventForm" style="display: none;">
                <div class="form-group">
                    <label>Descrizione Scadenza *</label>
                    <input type="text" name="descrizione" id="descrizioneScadenza" placeholder="es. Acconto 30% - Nome Cliente">
                </div>
                <div class="form-group">
                    <label>Progetto *</label>
                    <select name="progetto_id" id="progettoSelect">
                    <option value="">-- Seleziona Progetto --</option>
                    <?php
                    $progetti = $db->query("
                        SELECT p.id, p.titolo, c.ragione_sociale
                        FROM progetti p
                        LEFT JOIN clienti c ON p.cliente_id = c.id
                        WHERE p.stato != 'annullato'
                        ORDER BY c.ragione_sociale, p.titolo
                    ");
                    while ($p = $progetti->fetchArray(SQLITE3_ASSOC)) {
                        echo '<option value="' . $p['id'] . '">';
                        echo h($p['ragione_sociale']) . ' - ' . h($p['titolo']);
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Data Scadenza *</label>
                <input type="date" name="data_scadenza" required id="dataScadenza">
            </div>
            <div class="form-group">
                <label>Importo (€)</label>
                <input type="number" name="importo" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label>Tipo Scadenza</label>
                <select name="tipo">
                    <option value="pagamento" selected>Pagamento</option>
                    <option value="consegna">Consegna</option>
                    <option value="milestone">Milestone</option>
                    <option value="riunione">Riunione</option>
                    <option value="altro">Altro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Note</label>
                <textarea name="note"></textarea>
            </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="fas fa-save"></i> Salva Evento
                </button>
                <button type="button" onclick="closeNewEventModal()" class="btn btn-secondary">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/it.js"></script>

<script>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'it',
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
        firstDay: 1, // Lunedì
        navLinks: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,

        // Carica eventi
        events: function(info, successCallback, failureCallback) {
            fetch('calendar_api.php?action=events&start=' + info.startStr + '&end=' + info.endStr)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Errore caricamento eventi:', error);
                    failureCallback(error);
                });
        },

        // Click su evento
        eventClick: function(info) {
            showEventDetail(info.event);
        },

        // Selezione date (crea nuovo evento)
        select: function(info) {
            openNewEventModal(info.startStr);
        },

        // Drag & drop evento
        eventDrop: function(info) {
            updateEventDate(info.event);
        },

        // Resize evento
        eventResize: function(info) {
            updateEventDate(info.event);
        }
    });

    calendar.render();
});

// Mostra dettaglio evento
function showEventDetail(event) {
    const modal = document.getElementById('eventModal');
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');

    title.innerHTML = '<i class="fas fa-calendar-alt"></i> ' + event.title;

    let html = '';

    if (event.extendedProps.type === 'scadenza') {
        html = `
            <div style="margin-bottom: 15px;">
                <strong>Tipo Scadenza:</strong> ${event.extendedProps.tipo || 'pagamento'}<br>
                <strong>Data:</strong> ${formatDate(event.start)}<br>
                <strong>Progetto:</strong> ${event.extendedProps.progetto || '-'}<br>
                <strong>Cliente:</strong> ${event.extendedProps.cliente || '-'}<br>
                <strong>Importo:</strong> ${event.extendedProps.importo || '-'}<br>
                <strong>Stato:</strong> <span class="badge badge-${event.extendedProps.stato}">${event.extendedProps.stato}</span>
            </div>
            ${event.extendedProps.note ? '<div style="margin-bottom: 15px;"><strong>Note:</strong><br>' + event.extendedProps.note + '</div>' : ''}
            <div style="display: flex; gap: 10px;">
                <a href="scadenze.php?id=${event.extendedProps.id}" class="btn">
                    <i class="fas fa-edit"></i> Modifica
                </a>
                <button onclick="closeModal()" class="btn btn-secondary">Chiudi</button>
            </div>
        `;
    } else if (event.extendedProps.type === 'generic') {
        const tipiEvento = {
            'nota': '📝 Nota',
            'appuntamento': '📅 Appuntamento',
            'promemoria': '⏰ Promemoria',
            'riunione': '👥 Riunione',
            'telefonata': '📞 Telefonata',
            'email': '📧 Email',
            'altro': '🔖 Altro'
        };

        html = `
            <div style="margin-bottom: 15px;">
                <strong>Tipo:</strong> ${tipiEvento[event.extendedProps.tipo_evento] || event.extendedProps.tipo_evento}<br>
                <strong>Data Inizio:</strong> ${formatDate(event.start)}<br>
                ${event.end ? '<strong>Data Fine:</strong> ' + formatDate(event.end) + '<br>' : ''}
                <strong>Tutto il giorno:</strong> ${event.allDay ? 'Sì' : 'No'}
            </div>
            ${event.extendedProps.descrizione ? '<div style="margin-bottom: 15px;"><strong>Descrizione:</strong><br>' + event.extendedProps.descrizione + '</div>' : ''}
            <div style="padding: 10px; border-radius: 6px; margin-bottom: 15px; background: ' + event.backgroundColor + '; color: white;">
                <i class="fas fa-bookmark"></i> Evento personale
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="deleteEvent('generic', ${event.extendedProps.id})" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Elimina
                </button>
                <button onclick="closeModal()" class="btn btn-secondary">Chiudi</button>
            </div>
        `;
    } else if (event.extendedProps.type === 'google') {
        html = `
            <div style="margin-bottom: 15px;">
                <strong>Tipo:</strong> <i class="fab fa-google"></i> Evento Google Calendar<br>
                <strong>Data:</strong> ${formatDate(event.start)}
                ${event.end ? '<br><strong>Fine:</strong> ' + formatDate(event.end) : ''}
            </div>
            ${event.extendedProps.description ? '<div style="margin-bottom: 15px;"><strong>Descrizione:</strong><br>' + event.extendedProps.description + '</div>' : ''}
            <div style="padding: 10px; background: #e3f2fd; border-radius: 6px; margin-bottom: 15px;">
                <i class="fas fa-info-circle"></i> Questo evento proviene da Google Calendar
            </div>
            <button onclick="closeModal()" class="btn btn-secondary">Chiudi</button>
        `;
    }

    body.innerHTML = html;
    modal.style.display = 'flex';
}

// Apri modal nuovo evento
function openNewEventModal(date) {
    // Reset form
    document.getElementById('eventTypeSelect').value = 'generic';
    toggleEventForm();

    // Imposta data
    document.getElementById('dataScadenza').value = date;
    document.getElementById('dataInizioGeneric').value = date + 'T09:00';

    document.getElementById('newEventModal').style.display = 'flex';
}

// Cambia form tra generico e scadenza
function toggleEventForm() {
    const type = document.getElementById('eventTypeSelect').value;
    const genericForm = document.getElementById('genericEventForm');
    const scadenzaForm = document.getElementById('scadenzaEventForm');
    const title = document.getElementById('modalNewEventTitle');
    const submitBtn = document.getElementById('submitBtn');
    const titoloGeneric = document.getElementById('titoloGeneric');
    const descrizioneScadenza = document.getElementById('descrizioneScadenza');
    const progettoSelect = document.getElementById('progettoSelect');

    if (type === 'generic') {
        genericForm.style.display = 'block';
        scadenzaForm.style.display = 'none';
        title.innerHTML = '<i class="fas fa-calendar-plus"></i> Nuovo Evento';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Salva Evento';

        // Required fields
        titoloGeneric.required = true;
        descrizioneScadenza.required = false;
        progettoSelect.required = false;
    } else {
        genericForm.style.display = 'none';
        scadenzaForm.style.display = 'block';
        title.innerHTML = '<i class="fas fa-money-bill-wave"></i> Nuova Scadenza';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Salva Scadenza';

        // Required fields
        titoloGeneric.required = false;
        descrizioneScadenza.required = true;
        progettoSelect.required = true;
    }
}

// Toggle campi ora per eventi "tutto il giorno"
function toggleTimeFields() {
    const tuttoIlGiorno = document.getElementById('tuttoIlGiorno').checked;
    const dataInizio = document.getElementById('dataInizioGeneric');

    if (tuttoIlGiorno) {
        // Converti in date (solo data senza ora)
        if (dataInizio.value) {
            dataInizio.type = 'date';
            dataInizio.value = dataInizio.value.split('T')[0];
        }
    } else {
        // Converti in datetime-local (data + ora)
        dataInizio.type = 'datetime-local';
        if (dataInizio.value && !dataInizio.value.includes('T')) {
            dataInizio.value += 'T09:00';
        }
    }
}

// Chiudi modal
function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function closeNewEventModal() {
    document.getElementById('newEventModal').style.display = 'none';
    document.getElementById('newEventForm').reset();
}

// Salva nuovo evento (scadenza o generico)
document.getElementById('newEventForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const eventType = formData.get('event_type');

    const action = eventType === 'generic' ? 'create_generic' : 'create';

    fetch('calendar_api.php?action=' + action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeNewEventModal();
            calendar.refetchEvents();
            const msg = eventType === 'generic' ? 'Evento creato con successo!' : 'Scadenza creata con successo!';
            alert(msg);
        } else {
            alert('Errore: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        alert('Errore durante il salvataggio');
    });
});

// Aggiorna data evento (drag & drop)
function updateEventDate(event) {
    if (event.extendedProps.type === 'google') {
        alert('Non puoi modificare eventi di Google Calendar da qui');
        calendar.refetchEvents();
        return;
    }

    const formData = new FormData();
    formData.append('id', event.extendedProps.id);
    formData.append('type', event.extendedProps.type);
    formData.append('new_date', event.startStr);

    fetch('calendar_api.php?action=update_date', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Data aggiornata');
        } else {
            alert('Errore: ' + data.error);
            calendar.refetchEvents();
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        calendar.refetchEvents();
    });
}

// Elimina evento
function deleteEvent(type, id) {
    if (!confirm('Sei sicuro di voler eliminare questo evento?')) {
        return;
    }

    const formData = new FormData();
    formData.append('type', type);
    formData.append('id', id);

    fetch('calendar_api.php?action=delete_event', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            calendar.refetchEvents();
            alert('Evento eliminato con successo!');
        } else {
            alert('Errore: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        alert('Errore durante l\'eliminazione');
    });
}

// Sincronizza con Google Calendar
function syncNow() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sincronizzazione...';

    fetch('calendar_api.php?action=sync_google')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                alert('Sincronizzazione completata!\n' +
                      'Eventi importati: ' + (data.imported || 0) + '\n' +
                      'Eventi esportati: ' + (data.exported || 0));
            } else {
                alert('Errore sincronizzazione: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Errore durante la sincronizzazione');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync"></i> Sincronizza Ora';
        });
}

// Formatta data
function formatDate(date) {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Chiudi modal cliccando fuori
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

<div style="margin-top: 20px;">
    <a href="scadenze.php" class="btn btn-secondary">
        <i class="fas fa-list"></i> Vista Lista Scadenze
    </a>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
