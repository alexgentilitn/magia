@extends('layouts.cliente')

@section('titolo', 'Calendario')

@section('contenuto')

<!-- Back Button (solo mobile) -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium md:hidden">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header -->
<div class="gradient-magia rounded-2xl p-8 text-white text-center mb-6">
    <div class="text-6xl mb-4">📅</div>
    <h1 class="text-4xl font-bold mb-2">Il Mio Calendario</h1>
    <p class="text-xl text-white text-opacity-90">Le tue lezioni e appuntamenti</p>
</div>

<!-- Filtri Veloci -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <div class="flex overflow-x-auto space-x-3 pb-2">
        <button onclick="filterCalendar('tutti')" id="filter-tutti" class="filter-btn whitespace-nowrap px-4 py-2 rounded-lg font-semibold bg-fucsia-magia text-white">
            Tutti
        </button>
        <button onclick="filterCalendar('ballo')" id="filter-ballo" class="filter-btn whitespace-nowrap px-4 py-2 rounded-lg font-semibold bg-gray-100 text-gray-700 hover:bg-arancio-magia hover:text-white transition">
            🏃‍♀️ Ballo
        </button>
        <button onclick="filterCalendar('alimentazione')" id="filter-alimentazione" class="filter-btn whitespace-nowrap px-4 py-2 rounded-lg font-semibold bg-gray-100 text-gray-700 hover:bg-fucsia-magia hover:text-white transition">
            🥗 Alimentazione
        </button>
        <button onclick="filterCalendar('pelle')" id="filter-pelle" class="filter-btn whitespace-nowrap px-4 py-2 rounded-lg font-semibold bg-gray-100 text-gray-700 hover:bg-viola-magia hover:text-white transition">
            ✨ Pelle
        </button>
        <button onclick="filterCalendar('coaching')" id="filter-coaching" class="filter-btn whitespace-nowrap px-4 py-2 rounded-lg font-semibold bg-gray-100 text-gray-700 hover:bg-viola-magia hover:text-white transition">
            👑 Coaching
        </button>
    </div>
</div>

<!-- Prossimi Appuntamenti -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="far fa-clock text-arancio-magia mr-2"></i>
        Prossimi Appuntamenti
    </h2>

    <!-- Appuntamento Esempio 1 -->
    <div class="event-card mb-4 p-4 border-l-4 border-arancio-magia bg-arancio-magia bg-opacity-5 rounded-lg" data-category="ballo">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-arancio-magia rounded-lg flex flex-col items-center justify-center text-white">
                    <span class="text-xs font-semibold">LUN</span>
                    <span class="text-xl font-bold">{{ date('d') }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Lezione di Ballo - Balla & Snella</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span><i class="far fa-clock mr-1"></i> 18:00 - 19:30</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Sede Centro</span>
                    </div>
                </div>
            </div>
            <button class="text-arancio-magia hover:text-arancio-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-check mr-1"></i> Confermata
            </span>
            <span class="text-xs text-gray-500">Istruttrice: Laura Rossi</span>
        </div>
    </div>

    <!-- Appuntamento Esempio 2 -->
    <div class="event-card mb-4 p-4 border-l-4 border-fucsia-magia bg-fucsia-magia bg-opacity-5 rounded-lg" data-category="alimentazione">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-fucsia-magia rounded-lg flex flex-col items-center justify-center text-white">
                    <span class="text-xs font-semibold">MAR</span>
                    <span class="text-xl font-bold">{{ date('d', strtotime('+1 day')) }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Consulenza Nutrizionale</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span><i class="far fa-clock mr-1"></i> 15:00 - 15:45</span>
                        <span><i class="fas fa-video mr-1"></i> Online</span>
                    </div>
                </div>
            </div>
            <button class="text-fucsia-magia hover:text-fucsia-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                <i class="far fa-clock mr-1"></i> Da confermare
            </span>
            <span class="text-xs text-gray-500">Nutrizionista: Dr.ssa Maria Bianchi</span>
        </div>
    </div>

    <!-- Appuntamento Esempio 3 -->
    <div class="event-card mb-4 p-4 border-l-4 border-viola-magia bg-viola-magia bg-opacity-5 rounded-lg" data-category="pelle">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-viola-magia rounded-lg flex flex-col items-center justify-center text-white">
                    <span class="text-xs font-semibold">GIO</span>
                    <span class="text-xl font-bold">{{ date('d', strtotime('+3 days')) }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Trattamento Viso - Pelle & Benessere</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span><i class="far fa-clock mr-1"></i> 10:30 - 11:30</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Sede Beauty</span>
                    </div>
                </div>
            </div>
            <button class="text-viola-magia hover:text-viola-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-check mr-1"></i> Confermata
            </span>
            <span class="text-xs text-gray-500">Estetista: Sofia Verdi</span>
        </div>
    </div>

    <!-- Appuntamento Esempio 4 -->
    <div class="event-card mb-4 p-4 border-l-4 border-arancio-magia bg-arancio-magia bg-opacity-5 rounded-lg" data-category="ballo">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-arancio-magia rounded-lg flex flex-col items-center justify-center text-white">
                    <span class="text-xs font-semibold">VEN</span>
                    <span class="text-xl font-bold">{{ date('d', strtotime('+4 days')) }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Lezione di Ballo - Balla & Snella</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span><i class="far fa-clock mr-1"></i> 18:00 - 19:30</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Sede Centro</span>
                    </div>
                </div>
            </div>
            <button class="text-arancio-magia hover:text-arancio-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-check mr-1"></i> Confermata
            </span>
            <span class="text-xs text-gray-500">Istruttrice: Laura Rossi</span>
        </div>
    </div>
</div>

<!-- Vista Calendario Mensile (Semplificata) -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="far fa-calendar-alt text-fucsia-magia mr-2"></i>
            {{ date('F Y') }}
        </h2>
        <div class="flex space-x-2">
            <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                <i class="fas fa-chevron-left text-gray-600"></i>
            </button>
            <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                <i class="fas fa-chevron-right text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- Calendario Grid -->
    <div class="grid grid-cols-7 gap-2">
        <!-- Header giorni della settimana -->
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Lun</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Mar</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Mer</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Gio</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Ven</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Sab</div>
        <div class="text-center text-xs font-semibold text-gray-500 py-2">Dom</div>

        @php
            $today = date('j');
            $daysInMonth = date('t');
            $firstDayOfMonth = date('N', strtotime(date('Y-m-01')));
        @endphp

        <!-- Giorni vuoti iniziali -->
        @for($i = 1; $i < $firstDayOfMonth; $i++)
            <div class="aspect-square"></div>
        @endfor

        <!-- Giorni del mese -->
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $isToday = ($day == $today);
                $hasEvent = in_array($day, [
                    $today,
                    $today + 1,
                    $today + 3,
                    $today + 4,
                    $today + 7,
                    $today + 10
                ]);
            @endphp
            <div class="aspect-square p-1">
                <div class="h-full rounded-lg {{ $isToday ? 'bg-fucsia-magia text-white font-bold' : ($hasEvent ? 'bg-arancio-magia bg-opacity-10 border border-arancio-magia' : 'bg-gray-50 hover:bg-gray-100') }} flex flex-col items-center justify-center cursor-pointer transition">
                    <span class="text-sm">{{ $day }}</span>
                    @if($hasEvent && !$isToday)
                        <div class="flex space-x-1 mt-1">
                            <div class="w-1 h-1 rounded-full bg-arancio-magia"></div>
                        </div>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    <div class="mt-4 text-center">
        <p class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Clicca su un giorno per vedere i dettagli o prenotare una nuova lezione
        </p>
    </div>
</div>

<!-- Azioni Rapide -->
<div class="bg-white rounded-2xl shadow-sm p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-bolt text-arancio-magia mr-2"></i>
        Azioni Rapide
    </h2>

    <div class="grid md:grid-cols-3 gap-4">
        <button onclick="alert('Funzionalità di prenotazione in arrivo!')" class="p-4 bg-gradient-to-br from-arancio-magia to-orange-400 rounded-xl text-white hover:shadow-xl transition">
            <i class="fas fa-plus-circle text-3xl mb-2"></i>
            <h3 class="font-bold">Prenota Lezione</h3>
            <p class="text-xs opacity-90">Aggiungi un appuntamento</p>
        </button>

        <button onclick="alert('Funzionalità in arrivo!')" class="p-4 bg-gradient-to-br from-fucsia-magia to-pink-400 rounded-xl text-white hover:shadow-xl transition">
            <i class="fas fa-history text-3xl mb-2"></i>
            <h3 class="font-bold">Storico</h3>
            <p class="text-xs opacity-90">Vedi lezioni passate</p>
        </button>

        <button onclick="alert('Funzionalità in arrivo!')" class="p-4 bg-gradient-to-br from-viola-magia to-purple-400 rounded-xl text-white hover:shadow-xl transition">
            <i class="fas fa-download text-3xl mb-2"></i>
            <h3 class="font-bold">Esporta</h3>
            <p class="text-xs opacity-90">Scarica calendario</p>
        </button>
    </div>
</div>

<!-- Legenda -->
<div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
    <h3 class="font-bold text-gray-800 mb-4">Legenda</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-arancio-magia rounded"></div>
            <span class="text-sm text-gray-700">Ballo</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-fucsia-magia rounded"></div>
            <span class="text-sm text-gray-700">Alimentazione</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-viola-magia rounded"></div>
            <span class="text-sm text-gray-700">Pelle & Benessere</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded"></div>
            <span class="text-sm text-gray-700">Coaching</span>
        </div>
    </div>
</div>

<!-- Footer Quote -->
<div class="text-center mt-8 mb-4">
    <p class="text-viola-magia font-semibold text-lg italic">
        ✨ Ogni giorno è un passo verso il tuo benessere ✨
    </p>
</div>

@push('scripts')
<script>
function filterCalendar(category) {
    const allCards = document.querySelectorAll('.event-card');
    const allButtons = document.querySelectorAll('.filter-btn');

    // Reset button styles
    allButtons.forEach(btn => {
        btn.classList.remove('bg-fucsia-magia', 'bg-arancio-magia', 'bg-viola-magia', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });

    // Activate selected button
    const activeButton = document.getElementById('filter-' + category);
    activeButton.classList.remove('bg-gray-100', 'text-gray-700');

    if (category === 'tutti') {
        activeButton.classList.add('bg-fucsia-magia', 'text-white');
        allCards.forEach(card => card.style.display = 'block');
    } else {
        if (category === 'ballo') {
            activeButton.classList.add('bg-arancio-magia', 'text-white');
        } else if (category === 'alimentazione') {
            activeButton.classList.add('bg-fucsia-magia', 'text-white');
        } else {
            activeButton.classList.add('bg-viola-magia', 'text-white');
        }

        allCards.forEach(card => {
            if (card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
}
</script>
@endpush

@endsection
