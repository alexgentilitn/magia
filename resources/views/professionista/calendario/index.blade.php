@extends('layouts.professionista')

@section('titolo', 'Calendario')
@section('sottotitolo', 'Visualizza il tuo calendario delle lezioni')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Calendario -->
    <div class="bg-white rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/it.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'it',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Oggi',
            month: 'Mese',
            week: 'Settimana',
            day: 'Giorno'
        },
        events: function(info, successCallback, failureCallback) {
            fetch('{{ route('professionista.calendario.events') }}?start=' + info.startStr + '&end=' + info.endStr)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Errore caricamento eventi:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Mostra dettagli evento
            Swal.fire({
                title: info.event.title,
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Sede:</strong> ${info.event.extendedProps.sede}</p>
                        <p class="mb-2"><strong>Programma:</strong> ${info.event.extendedProps.programma}</p>
                        <p class="mb-2"><strong>Partecipanti:</strong> ${info.event.extendedProps.partecipanti}</p>
                        <p class="mb-2"><strong>Stato:</strong> ${info.event.extendedProps.stato}</p>
                        <p class="mb-2"><strong>Orario:</strong> ${info.event.start.toLocaleTimeString('it-IT', {hour: '2-digit', minute:'2-digit'})} - ${info.event.end.toLocaleTimeString('it-IT', {hour: '2-digit', minute:'2-digit'})}</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Vedi Dettagli',
                cancelButtonText: 'Chiudi',
                confirmButtonColor: '#7B2869',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ url('professionista/lezioni') }}/' + info.event.id;
                }
            });
        },
        eventDidMount: function(info) {
            // Tooltip
            info.el.title = info.event.extendedProps.sede;
        },
        height: 'auto',
        slotMinTime: '06:00:00',
        slotMaxTime: '23:00:00',
        allDaySlot: false,
    });

    calendar.render();
});
</script>
@endpush
