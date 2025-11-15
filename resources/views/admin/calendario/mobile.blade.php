@extends('layouts.admin')

@section('titolo', 'Calendario')

@section('contenuto')
<!-- Calendario Mobile Nativo -->
<div class="mobile-calendar bg-gray-50 min-h-screen pb-20">

    <!-- Header Sticky con Data e Navigazione -->
    <div class="sticky top-0 z-40 bg-white shadow-md">
        <!-- Top Bar -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <button id="btnFiltri" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>

            <h1 class="text-lg font-bold text-gray-800" id="currentDate">Oggi</h1>

            <button id="btnToday" class="px-3 py-1.5 text-sm font-medium text-fucsia-magia border border-fucsia-magia rounded-lg hover:bg-fucsia-magia hover:text-white transition-colors">
                Oggi
            </button>
        </div>

        <!-- Selettore Vista -->
        <div class="flex items-center border-b border-gray-200 bg-gray-50">
            <button data-view="day" class="view-selector flex-1 py-3 text-sm font-medium text-fucsia-magia border-b-2 border-fucsia-magia">
                Giorno
            </button>
            <button data-view="week" class="view-selector flex-1 py-3 text-sm font-medium text-gray-600">
                Settimana
            </button>
            <button data-view="list" class="view-selector flex-1 py-3 text-sm font-medium text-gray-600">
                Lista
            </button>
        </div>

        <!-- Navigazione Date con Swipe Visual -->
        <div class="flex items-center justify-between px-2 py-2 bg-white">
            <button id="btnPrev" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="text-center flex-1">
                <p class="text-xs text-gray-500 uppercase tracking-wide" id="currentMonth"></p>
                <p class="text-2xl font-bold text-gray-900" id="currentDay"></p>
                <p class="text-xs text-gray-600" id="currentWeekday"></p>
            </div>

            <button id="btnNext" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Contenitore Viste con Swipe -->
    <div id="viewContainer" class="relative overflow-hidden">
        <!-- Vista Giorno -->
        <div id="dayView" class="view-content active">
            <div class="px-4 py-4">
                <!-- Timeline Oraria -->
                <div id="dayTimeline" class="space-y-2">
                    <!-- Popolato dinamicamente con JS -->
                </div>

                <!-- Empty State -->
                <div id="emptyDay" class="hidden text-center py-16">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">Nessuna lezione in programma</p>
                    <p class="text-gray-400 text-sm mt-1">Tocca il pulsante + per crearne una</p>
                </div>
            </div>
        </div>

        <!-- Vista Settimana -->
        <div id="weekView" class="view-content hidden">
            <div class="px-4 py-4">
                <!-- Griglia Settimana Scrollabile Orizzontalmente -->
                <div id="weekScroller" class="overflow-x-auto -mx-4 px-4 pb-4">
                    <div id="weekGrid" class="flex gap-3 min-w-max">
                        <!-- Popolato dinamicamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista Lista -->
        <div id="listView" class="view-content hidden">
            <div id="listContainer" class="divide-y divide-gray-200">
                <!-- Popolato dinamicamente con infinite scroll -->
            </div>

            <!-- Loading Indicator -->
            <div id="listLoading" class="hidden py-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-fucsia-magia"></div>
            </div>
        </div>
    </div>

    <!-- Pull to Refresh Indicator -->
    <div id="pullRefreshIndicator" class="fixed top-16 left-1/2 transform -translate-x-1/2 bg-white rounded-full shadow-lg px-4 py-2 flex items-center gap-2 transition-all duration-300 opacity-0 scale-90 pointer-events-none z-30">
        <svg class="w-5 h-5 text-fucsia-magia animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span class="text-sm font-medium text-gray-700">Aggiornamento...</span>
    </div>

</div>

<!-- Bottom Sheet Dettagli Lezione -->
<div id="bottomSheet" class="fixed inset-0 z-50 pointer-events-none">
    <!-- Backdrop -->
    <div id="sheetBackdrop" class="absolute inset-0 bg-black transition-opacity duration-300 opacity-0"></div>

    <!-- Sheet Content -->
    <div id="sheetContent" class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl transform translate-y-full transition-transform duration-300 pointer-events-auto max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Handle -->
        <div class="pt-3 pb-2 flex justify-center">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <!-- Content -->
        <div id="sheetBody" class="flex-1 overflow-y-auto px-4 pb-6">
            <!-- Popolato dinamicamente -->
        </div>
    </div>
</div>

<!-- Drawer Filtri -->
<div id="filterDrawer" class="fixed inset-0 z-50 pointer-events-none">
    <!-- Backdrop -->
    <div id="filterBackdrop" class="absolute inset-0 bg-black transition-opacity duration-300 opacity-0"></div>

    <!-- Drawer Content -->
    <div id="filterContent" class="absolute top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 pointer-events-auto overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Filtri</h2>
                <button id="closeFilters" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Filtri -->
            <div class="space-y-4">
                <!-- Professionista -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Professionista</label>
                    <select id="filterProfessionista" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Tutti</option>
                        @foreach($professionisti as $prof)
                            <option value="{{ $prof->utente_id }}">{{ $prof->nome }} {{ $prof->cognome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sede -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                    <select id="filterSede" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Tutte</option>
                        @foreach($sedi as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipologia -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipologia</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="filter-chip">
                            <input type="checkbox" name="tipologia" value="gruppo" class="hidden peer">
                            <span class="block px-3 py-2 text-center text-sm font-medium border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-fucsia-magia peer-checked:bg-fucsia-magia peer-checked:text-white transition-colors">
                                Gruppo
                            </span>
                        </label>
                        <label class="filter-chip">
                            <input type="checkbox" name="tipologia" value="individuale" class="hidden peer">
                            <span class="block px-3 py-2 text-center text-sm font-medium border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-fucsia-magia peer-checked:bg-fucsia-magia peer-checked:text-white transition-colors">
                                Individuale
                            </span>
                        </label>
                        <label class="filter-chip">
                            <input type="checkbox" name="tipologia" value="online" class="hidden peer">
                            <span class="block px-3 py-2 text-center text-sm font-medium border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-fucsia-magia peer-checked:bg-fucsia-magia peer-checked:text-white transition-colors">
                                Online
                            </span>
                        </label>
                        <label class="filter-chip">
                            <input type="checkbox" name="tipologia" value="ibrida" class="hidden peer">
                            <span class="block px-3 py-2 text-center text-sm font-medium border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-fucsia-magia peer-checked:bg-fucsia-magia peer-checked:text-white transition-colors">
                                Ibrida
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Stato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                    <select id="filterStato" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Tutti</option>
                        <option value="programmata">Programmata</option>
                        <option value="confermata">Confermata</option>
                        <option value="in_corso">In Corso</option>
                        <option value="completata">Completata</option>
                        <option value="cancellata">Cancellata</option>
                    </select>
                </div>
            </div>

            <!-- Azioni Filtri -->
            <div class="mt-6 space-y-2">
                <button id="applyFilters" class="w-full px-4 py-3 bg-fucsia-magia text-white rounded-lg font-medium hover:bg-viola-magia transition-colors">
                    Applica Filtri
                </button>
                <button id="resetFilters" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    Reset
                </button>
            </div>

            <!-- Legenda -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Legenda Colori</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: #9c27b0;"></div>
                        <span>Gruppo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: #e91e63;"></div>
                        <span>Individuale</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: #2196f3;"></div>
                        <span>Online</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: #ff9800;"></div>
                        <span>Ibrida</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAB - Crea Lezione -->
<button id="fabCreate" class="fixed bottom-6 right-6 w-14 h-14 bg-fucsia-magia text-white rounded-full shadow-lg hover:bg-viola-magia active:scale-95 transition-all duration-200 z-40 flex items-center justify-center">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
    </svg>
</button>

@endsection

@push('styles')
<style>
    /* Previeni scroll durante swipe */
    .swiping {
        overflow: hidden;
        touch-action: none;
    }

    /* Smooth transitions per le viste */
    .view-content {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .view-content.hidden {
        display: none;
    }

    /* Animazione swipe */
    .swipe-transition {
        transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    /* Bottom Sheet aperto */
    #bottomSheet.open {
        pointer-events: auto;
    }

    #bottomSheet.open #sheetBackdrop {
        opacity: 0.5;
    }

    #bottomSheet.open #sheetContent {
        transform: translateY(0);
    }

    /* Filter Drawer aperto */
    #filterDrawer.open {
        pointer-events: auto;
    }

    #filterDrawer.open #filterBackdrop {
        opacity: 0.5;
    }

    #filterDrawer.open #filterContent {
        transform: translateX(0);
    }

    /* Card lezione */
    .lesson-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        -webkit-tap-highlight-color: transparent;
    }

    .lesson-card:active {
        transform: scale(0.98);
    }

    /* Timeline oraria */
    .time-slot {
        min-height: 60px;
        position: relative;
    }

    .time-label {
        width: 60px;
        flex-shrink: 0;
    }

    /* Vista selezionata */
    .view-selector {
        position: relative;
        transition: color 0.2s ease;
        border-bottom: 2px solid transparent;
    }

    .view-selector.active {
        color: #e91e63;
        border-bottom-color: #e91e63;
    }

    /* Pull to refresh */
    #pullRefreshIndicator.active {
        opacity: 1;
        scale: 1;
    }

    /* Ottimizzazioni touch */
    button, a, .clickable {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    /* Scroll smooth */
    .overflow-y-auto, .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    /* Badge tipologie */
    .badge-gruppo { background-color: #9c27b0; }
    .badge-individuale { background-color: #e91e63; }
    .badge-online { background-color: #2196f3; }
    .badge-ibrida { background-color: #ff9800; }

    /* Badge stati */
    .badge-programmata { background-color: #ffa726; }
    .badge-confermata { background-color: #66bb6a; }
    .badge-in_corso { background-color: #42a5f5; }
    .badge-completata { background-color: #26a69a; }
    .badge-cancellata { background-color: #ef5350; }

    /* Card settimana */
    .week-day-card {
        min-width: 120px;
        scroll-snap-align: start;
    }

    #weekScroller {
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
    }

    #weekScroller::-webkit-scrollbar {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ============================================================================
// CALENDARIO MOBILE NATIVO
// ============================================================================

class MobileCalendar {
    constructor() {
        this.currentDate = new Date();
        this.currentView = 'day';
        this.events = [];
        this.filters = {
            professionista_id: '',
            sede_id: '',
            tipologia: [],
            stato: ''
        };

        // Touch tracking
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchCurrentX = 0;
        this.isSwiping = false;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateDateDisplay();
        this.loadEvents();
    }

    setupEventListeners() {
        // Navigazione
        document.getElementById('btnPrev').addEventListener('click', () => this.navigateDate(-1));
        document.getElementById('btnNext').addEventListener('click', () => this.navigateDate(1));
        document.getElementById('btnToday').addEventListener('click', () => this.goToToday());

        // Cambio vista
        document.querySelectorAll('.view-selector').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchView(e.target.dataset.view);
            });
        });

        // Filtri
        document.getElementById('btnFiltri').addEventListener('click', () => this.openFilters());
        document.getElementById('closeFilters').addEventListener('click', () => this.closeFilters());
        document.getElementById('filterBackdrop').addEventListener('click', () => this.closeFilters());
        document.getElementById('applyFilters').addEventListener('click', () => this.applyFilters());
        document.getElementById('resetFilters').addEventListener('click', () => this.resetFilters());

        // FAB
        document.getElementById('fabCreate').addEventListener('click', () => this.createLesson());

        // Bottom Sheet
        document.getElementById('sheetBackdrop').addEventListener('click', () => this.closeBottomSheet());

        // Touch/Swipe gestures
        const viewContainer = document.getElementById('viewContainer');
        viewContainer.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
        viewContainer.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
        viewContainer.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: true });

        // Pull to refresh
        this.setupPullToRefresh();

        // Infinite scroll per vista lista
        this.setupInfiniteScroll();
    }

    // ========================================================================
    // NAVIGAZIONE DATE
    // ========================================================================

    navigateDate(direction) {
        if (this.currentView === 'day') {
            this.currentDate.setDate(this.currentDate.getDate() + direction);
        } else if (this.currentView === 'week') {
            this.currentDate.setDate(this.currentDate.getDate() + (direction * 7));
        }

        this.updateDateDisplay();
        this.loadEvents();
    }

    goToToday() {
        this.currentDate = new Date();
        this.updateDateDisplay();
        this.loadEvents();
    }

    updateDateDisplay() {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateString = this.currentDate.toLocaleDateString('it-IT', options);

        document.getElementById('currentDate').textContent = dateString;
        document.getElementById('currentMonth').textContent = this.currentDate.toLocaleDateString('it-IT', { month: 'long', year: 'numeric' }).toUpperCase();
        document.getElementById('currentDay').textContent = this.currentDate.getDate();
        document.getElementById('currentWeekday').textContent = this.currentDate.toLocaleDateString('it-IT', { weekday: 'long' });
    }

    // ========================================================================
    // CAMBIO VISTA
    // ========================================================================

    switchView(view) {
        this.currentView = view;

        // Update UI
        document.querySelectorAll('.view-selector').forEach(btn => {
            btn.classList.remove('active', 'text-fucsia-magia', 'border-b-2', 'border-fucsia-magia');
            btn.classList.add('text-gray-600');
        });

        const activeBtn = document.querySelector(`[data-view="${view}"]`);
        activeBtn.classList.add('active', 'text-fucsia-magia', 'border-b-2', 'border-fucsia-magia');
        activeBtn.classList.remove('text-gray-600');

        // Switch content
        document.querySelectorAll('.view-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.getElementById(`${view}View`).classList.remove('hidden');

        this.loadEvents();
    }

    // ========================================================================
    // CARICAMENTO EVENTI
    // ========================================================================

    async loadEvents() {
        try {
            const params = new URLSearchParams();

            if (this.currentView === 'day') {
                const dateStr = this.formatDate(this.currentDate);
                params.append('start', dateStr);
                params.append('end', dateStr);
            } else if (this.currentView === 'week') {
                const startWeek = this.getWeekStart(this.currentDate);
                const endWeek = this.getWeekEnd(this.currentDate);
                params.append('start', this.formatDate(startWeek));
                params.append('end', this.formatDate(endWeek));
            } else {
                // Lista: carica prossimi 30 giorni
                const today = new Date();
                const future = new Date();
                future.setDate(future.getDate() + 30);
                params.append('start', this.formatDate(today));
                params.append('end', this.formatDate(future));
            }

            // Aggiungi filtri
            if (this.filters.professionista_id) params.append('professionista_id', this.filters.professionista_id);
            if (this.filters.sede_id) params.append('sede_id', this.filters.sede_id);
            if (this.filters.tipologia.length > 0) params.append('tipologia', this.filters.tipologia.join(','));
            if (this.filters.stato) params.append('stato', this.filters.stato);

            const response = await fetch(`{{ route("admin.calendario.events") }}?${params.toString()}`);
            this.events = await response.json();

            this.renderView();
        } catch (error) {
            console.error('Errore caricamento eventi:', error);
            this.showError('Errore nel caricamento degli eventi');
        }
    }

    // ========================================================================
    // RENDERING VISTE
    // ========================================================================

    renderView() {
        if (this.currentView === 'day') {
            this.renderDayView();
        } else if (this.currentView === 'week') {
            this.renderWeekView();
        } else {
            this.renderListView();
        }
    }

    renderDayView() {
        const timeline = document.getElementById('dayTimeline');
        const empty = document.getElementById('emptyDay');

        const todayEvents = this.getEventsForDate(this.currentDate);

        if (todayEvents.length === 0) {
            timeline.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');

        // Genera timeline da 6:00 a 23:00
        let html = '';

        for (let hour = 6; hour < 23; hour++) {
            const hourEvents = todayEvents.filter(event => {
                const eventHour = parseInt(event.start.split('T')[1].split(':')[0]);
                return eventHour === hour;
            });

            html += `
                <div class="time-slot flex gap-3">
                    <div class="time-label text-xs font-medium text-gray-500 pt-1">
                        ${hour.toString().padStart(2, '0')}:00
                    </div>
                    <div class="flex-1 space-y-2">
            `;

            if (hourEvents.length > 0) {
                hourEvents.forEach(event => {
                    html += this.renderEventCard(event);
                });
            } else {
                html += '<div class="h-px bg-gray-200 mt-2"></div>';
            }

            html += `
                    </div>
                </div>
            `;
        }

        timeline.innerHTML = html;

        // Aggiungi event listeners
        this.attachEventCardListeners();
    }

    renderWeekView() {
        const weekGrid = document.getElementById('weekGrid');

        const startWeek = this.getWeekStart(this.currentDate);
        let html = '';

        for (let i = 0; i < 7; i++) {
            const day = new Date(startWeek);
            day.setDate(day.getDate() + i);

            const dayEvents = this.getEventsForDate(day);
            const isToday = this.isSameDay(day, new Date());

            html += `
                <div class="week-day-card bg-white rounded-xl p-3 shadow-sm ${isToday ? 'ring-2 ring-fucsia-magia' : ''}">
                    <div class="text-center mb-3">
                        <p class="text-xs text-gray-500 uppercase">${day.toLocaleDateString('it-IT', { weekday: 'short' })}</p>
                        <p class="text-2xl font-bold ${isToday ? 'text-fucsia-magia' : 'text-gray-800'}">${day.getDate()}</p>
                    </div>
                    <div class="space-y-2">
            `;

            if (dayEvents.length > 0) {
                dayEvents.slice(0, 3).forEach(event => {
                    html += this.renderEventCardMini(event);
                });

                if (dayEvents.length > 3) {
                    html += `<p class="text-xs text-center text-gray-500">+${dayEvents.length - 3} altre</p>`;
                }
            } else {
                html += '<p class="text-xs text-center text-gray-400">Nessuna lezione</p>';
            }

            html += `
                    </div>
                </div>
            `;
        }

        weekGrid.innerHTML = html;
        this.attachEventCardListeners();
    }

    renderListView() {
        const listContainer = document.getElementById('listContainer');

        // Raggruppa eventi per data
        const groupedEvents = this.groupEventsByDate(this.events);

        let html = '';

        Object.keys(groupedEvents).forEach(dateKey => {
            const date = new Date(dateKey);
            const events = groupedEvents[dateKey];

            html += `
                <div class="py-4 px-4">
                    <div class="flex items-center gap-2 mb-3">
                        <h3 class="text-sm font-bold text-gray-700">
                            ${date.toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long' })}
                        </h3>
                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">${events.length}</span>
                    </div>
                    <div class="space-y-2">
            `;

            events.forEach(event => {
                html += this.renderEventCard(event);
            });

            html += `
                    </div>
                </div>
            `;
        });

        if (html === '') {
            html = `
                <div class="text-center py-16">
                    <p class="text-gray-500">Nessuna lezione trovata</p>
                </div>
            `;
        }

        listContainer.innerHTML = html;
        this.attachEventCardListeners();
    }

    // ========================================================================
    // RENDER EVENT CARDS
    // ========================================================================

    renderEventCard(event) {
        const startTime = event.start.split('T')[1].substring(0, 5);
        const endTime = event.end ? event.end.split('T')[1].substring(0, 5) : '';
        const tipologiaClass = `badge-${event.extendedProps.tipologia}`;

        return `
            <div class="lesson-card bg-white rounded-lg border-l-4 border-${tipologiaClass} shadow-sm p-3 cursor-pointer"
                 data-event-id="${event.id}">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 truncate mb-1">${event.title}</h4>
                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>${startTime}${endTime ? ' - ' + endTime : ''}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="${tipologiaClass} text-white text-xs px-2 py-0.5 rounded-full">
                                ${event.extendedProps.tipologia}
                            </span>
                            ${event.extendedProps.posti ? `
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded-full">
                                    ${event.extendedProps.posti}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        `;
    }

    renderEventCardMini(event) {
        const startTime = event.start.split('T')[1].substring(0, 5);
        const tipologiaClass = `badge-${event.extendedProps.tipologia}`;

        return `
            <div class="lesson-card ${tipologiaClass} text-white rounded-lg p-2 cursor-pointer text-xs"
                 data-event-id="${event.id}">
                <p class="font-medium truncate">${event.title}</p>
                <p class="opacity-90">${startTime}</p>
            </div>
        `;
    }

    attachEventCardListeners() {
        document.querySelectorAll('.lesson-card').forEach(card => {
            card.addEventListener('click', () => {
                const eventId = card.dataset.eventId;
                this.showEventDetails(eventId);
            });
        });
    }

    // ========================================================================
    // BOTTOM SHEET
    // ========================================================================

    async showEventDetails(eventId) {
        try {
            const response = await fetch(`{{ url('admin/calendario') }}/${eventId}`);
            const data = await response.json();

            const sheetBody = document.getElementById('sheetBody');
            sheetBody.innerHTML = data.html;

            this.openBottomSheet();
        } catch (error) {
            console.error('Errore:', error);
            this.showError('Errore nel caricamento dei dettagli');
        }
    }

    openBottomSheet() {
        const sheet = document.getElementById('bottomSheet');
        sheet.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    closeBottomSheet() {
        const sheet = document.getElementById('bottomSheet');
        sheet.classList.remove('open');
        document.body.style.overflow = '';
    }

    // ========================================================================
    // FILTRI
    // ========================================================================

    openFilters() {
        document.getElementById('filterDrawer').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    closeFilters() {
        document.getElementById('filterDrawer').classList.remove('open');
        document.body.style.overflow = '';
    }

    applyFilters() {
        this.filters.professionista_id = document.getElementById('filterProfessionista').value;
        this.filters.sede_id = document.getElementById('filterSede').value;
        this.filters.stato = document.getElementById('filterStato').value;

        this.filters.tipologia = Array.from(document.querySelectorAll('input[name="tipologia"]:checked'))
            .map(input => input.value);

        this.closeFilters();
        this.loadEvents();
    }

    resetFilters() {
        this.filters = {
            professionista_id: '',
            sede_id: '',
            tipologia: [],
            stato: ''
        };

        document.getElementById('filterProfessionista').value = '';
        document.getElementById('filterSede').value = '';
        document.getElementById('filterStato').value = '';
        document.querySelectorAll('input[name="tipologia"]').forEach(input => input.checked = false);

        this.closeFilters();
        this.loadEvents();
    }

    // ========================================================================
    // TOUCH/SWIPE GESTURES
    // ========================================================================

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
        this.isSwiping = false;
    }

    handleTouchMove(e) {
        if (!this.touchStartX) return;

        this.touchCurrentX = e.touches[0].clientX;
        const touchCurrentY = e.touches[0].clientY;

        const diffX = this.touchCurrentX - this.touchStartX;
        const diffY = touchCurrentY - this.touchStartY;

        // Determina se è swipe orizzontale
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 30) {
            this.isSwiping = true;
            e.preventDefault(); // Previeni scroll verticale
        }
    }

    handleTouchEnd(e) {
        if (!this.isSwiping) return;

        const diffX = this.touchCurrentX - this.touchStartX;
        const threshold = 50;

        if (Math.abs(diffX) > threshold) {
            if (diffX > 0) {
                // Swipe right -> giorno precedente
                this.navigateDate(-1);
            } else {
                // Swipe left -> giorno successivo
                this.navigateDate(1);
            }
        }

        this.touchStartX = 0;
        this.touchCurrentX = 0;
        this.isSwiping = false;
    }

    // ========================================================================
    // PULL TO REFRESH
    // ========================================================================

    setupPullToRefresh() {
        let startY = 0;
        let isPulling = false;
        const indicator = document.getElementById('pullRefreshIndicator');
        const threshold = 80;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (window.scrollY === 0 && startY > 0) {
                const currentY = e.touches[0].clientY;
                const diff = currentY - startY;

                if (diff > 0 && diff < threshold * 1.5) {
                    isPulling = true;
                    const progress = Math.min(diff / threshold, 1);
                    indicator.style.opacity = progress;
                    indicator.style.transform = `translateX(-50%) scale(${progress})`;
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', async () => {
            if (isPulling) {
                indicator.classList.add('active');

                await this.loadEvents();

                setTimeout(() => {
                    indicator.classList.remove('active');
                    indicator.style.opacity = '0';
                    indicator.style.transform = 'translateX(-50%) scale(0.9)';
                }, 800);

                isPulling = false;
                startY = 0;
            }
        }, { passive: true });
    }

    // ========================================================================
    // INFINITE SCROLL (Vista Lista)
    // ========================================================================

    setupInfiniteScroll() {
        const listContainer = document.getElementById('listContainer');
        const loading = document.getElementById('listLoading');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && this.currentView === 'list') {
                    // Carica più eventi
                    // TODO: implementare paginazione backend
                }
            });
        }, { threshold: 0.5 });

        observer.observe(loading);
    }

    // ========================================================================
    // CREA LEZIONE
    // ========================================================================

    createLesson() {
        const dateStr = this.formatDate(this.currentDate);
        window.location.href = `{{ route('admin.lezioni.create') }}?data=${dateStr}`;
    }

    // ========================================================================
    // UTILITY
    // ========================================================================

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    getWeekStart(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Lunedì
        return new Date(d.setDate(diff));
    }

    getWeekEnd(date) {
        const start = this.getWeekStart(date);
        const end = new Date(start);
        end.setDate(end.getDate() + 6);
        return end;
    }

    getEventsForDate(date) {
        const dateStr = this.formatDate(date);
        return this.events.filter(event => event.start.startsWith(dateStr));
    }

    isSameDay(date1, date2) {
        return this.formatDate(date1) === this.formatDate(date2);
    }

    groupEventsByDate(events) {
        const grouped = {};

        events.forEach(event => {
            const dateKey = event.start.split('T')[0];

            if (!grouped[dateKey]) {
                grouped[dateKey] = [];
            }

            grouped[dateKey].push(event);
        });

        return grouped;
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
}

// Inizializza calendario
document.addEventListener('DOMContentLoaded', () => {
    window.calendar = new MobileCalendar();
});
</script>
@endpush
