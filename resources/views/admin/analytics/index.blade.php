@extends('layouts.admin')

@section('titolo', 'Analytics')

@section('contenuto')
<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Analytics Comportamentali</h2>
            <p class="text-gray-600 mt-1">Analizza il comportamento degli utenti sulla piattaforma</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.analytics.eventi') }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                <i class="fas fa-list mr-2"></i>Tutti gli Eventi
            </a>
            <a href="{{ route('admin.analytics.export', ['periodo' => $periodo]) }}" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filtro Periodo -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Periodo:</label>
            <select name="periodo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia" onchange="this.form.submit()">
                <option value="7" {{ $periodo == 7 ? 'selected' : '' }}>Ultimi 7 giorni</option>
                <option value="30" {{ $periodo == 30 ? 'selected' : '' }}>Ultimi 30 giorni</option>
                <option value="90" {{ $periodo == 90 ? 'selected' : '' }}>Ultimi 90 giorni</option>
                <option value="365" {{ $periodo == 365 ? 'selected' : '' }}>Ultimo anno</option>
            </select>
        </form>
    </div>

    <!-- Statistiche Principali -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-viola-magia">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-1">Totale Eventi</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($statistiche['totale_eventi']) }}</p>
                </div>
                <div class="bg-viola-magia bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-chart-line text-viola-magia text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-1">Utenti Unici</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($statistiche['utenti_unici']) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-1">Page Views</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($statistiche['page_views']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-eye text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-fucsia-magia">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-1">Mobile %</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistiche['mobile_percent'] }}%</p>
                </div>
                <div class="bg-fucsia-magia bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-mobile-alt text-fucsia-magia text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Pagine Più Visitate -->
        <div class="bg-white rounded-lg shadow">
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-fire mr-2"></i>
                    Pagine Più Visitate
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($pagine_piu_visitate as $pagina)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-viola-magia bg-opacity-10 rounded-full p-2">
                                    <i class="fas fa-file text-viola-magia"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $pagina->pagina }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-fucsia-magia bg-opacity-10 text-fucsia-magia font-semibold rounded-full text-sm">
                                {{ number_format($pagina->visite) }} visite
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Nessun dato disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Dispositivi -->
        <div class="bg-white rounded-lg shadow">
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-desktop mr-2"></i>
                    Dispositivi
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($dispositivi as $dispositivo)
                        @php
                            $icons = [
                                'desktop' => 'desktop',
                                'mobile' => 'mobile-alt',
                                'tablet' => 'tablet-alt'
                            ];
                            $icon = $icons[$dispositivo->dispositivo] ?? 'question';
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 rounded-full p-2">
                                    <i class="fas fa-{{ $icon }} text-blue-600"></i>
                                </div>
                                <p class="font-medium text-gray-900 capitalize">{{ $dispositivo->dispositivo }}</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 font-semibold rounded-full text-sm">
                                {{ number_format($dispositivo->totale) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Browser -->
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-globe mr-2"></i>
                Browser Utilizzati
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($browser as $b)
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-900 text-lg">{{ $b->browser }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ number_format($b->totale) }} accessi</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
