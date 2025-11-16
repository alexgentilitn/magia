@extends('layouts.admin')

@section('titolo', 'Dashboard Conformità GDPR')

@section('contenuto')
<div class="p-6">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.privacy.index') }}" class="text-gray-700 hover:text-viola-magia">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Privacy GDPR
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Dashboard Conformità</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
            Dashboard Conformità GDPR
        </h2>
        <p class="text-gray-600 mt-1">Verifica compliance e identifica potenziali problemi</p>
    </div>

    <!-- Alert Riepilogo -->
    @php
        $totaleProblemi = $clienti_senza_privacy->count() + $consensi_scaduti->count();
    @endphp

    @if($totaleProblemi > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-red-800">
                    Attenzione! Rilevati {{ $totaleProblemi }} problemi di conformità
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @if($clienti_senza_privacy->count() > 0)
                            <li>{{ $clienti_senza_privacy->count() }} clienti senza Privacy Policy attiva</li>
                        @endif
                        @if($consensi_scaduti->count() > 0)
                            <li>{{ $consensi_scaduti->count() }} consensi scaduti (oltre 2 anni)</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-green-800">
                    Sistema Conforme GDPR
                </h3>
                <p class="mt-2 text-sm text-green-700">
                    Tutti i clienti hanno fornito i consensi necessari e non ci sono consensi scaduti.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistiche Conformità -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <!-- Card Clienti Senza Privacy -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Clienti Senza Privacy Policy</h3>
                        <p class="text-sm opacity-90 mt-1">Richiedono consenso obbligatorio</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <i class="fas fa-user-times text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-4xl font-bold">{{ $clienti_senza_privacy->count() }}</p>
                </div>
            </div>

            @if($clienti_senza_privacy->count() > 0)
            <div class="p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Azioni Richieste</h4>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($clienti_senza_privacy as $cliente)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100 hover:bg-red-100 transition">
                        <div class="flex items-center flex-1">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($cliente->nome, 0, 1) . substr($cliente->cognome, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $cliente->nome_completo }}</p>
                                <p class="text-xs text-gray-500">{{ $cliente->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.privacy.cliente', $cliente->id) }}"
                               class="px-3 py-1.5 bg-viola-magia text-white text-xs font-medium rounded hover:bg-fucsia-magia transition"
                               title="Visualizza consensi">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.clienti.edit', $cliente->id) }}"
                               class="px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition"
                               title="Modifica cliente">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="p-6 text-center text-gray-500">
                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                <p class="text-sm">Tutti i clienti hanno la Privacy Policy attiva</p>
            </div>
            @endif
        </div>

        <!-- Card Consensi Scaduti -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Consensi Scaduti</h3>
                        <p class="text-sm opacity-90 mt-1">Oltre 2 anni, richiedono rinnovo</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-4xl font-bold">{{ $consensi_scaduti->count() }}</p>
                </div>
            </div>

            @if($consensi_scaduti->count() > 0)
            <div class="p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Consensi da Rinnovare</h4>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($consensi_scaduti as $consenso)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-100 hover:bg-yellow-100 transition">
                        <div class="flex items-center flex-1">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($consenso->cliente->nome, 0, 1) . substr($consenso->cliente->cognome, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $consenso->cliente->nome_completo }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-500">
                                        {{ $consenso->tipo_consenso_label }}
                                    </span>
                                    <span class="text-xs text-red-600 font-medium">
                                        <i class="fas fa-calendar-times mr-1"></i>
                                        {{ $consenso->data_consenso->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.privacy.cliente', $consenso->cliente_id) }}"
                               class="px-3 py-1.5 bg-viola-magia text-white text-xs font-medium rounded hover:bg-fucsia-magia transition"
                               title="Visualizza consensi">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="p-6 text-center text-gray-500">
                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                <p class="text-sm">Nessun consenso scaduto</p>
            </div>
            @endif
        </div>

    </div>

    <!-- Checklist Conformità GDPR -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-tasks mr-2"></i>
                Checklist Conformità GDPR
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">

                <!-- Check 1: Privacy Policy -->
                <div class="flex items-start p-4 rounded-lg {{ $clienti_senza_privacy->count() == 0 ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500' }}">
                    <div class="flex-shrink-0">
                        @if($clienti_senza_privacy->count() == 0)
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                        @endif
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold {{ $clienti_senza_privacy->count() == 0 ? 'text-green-900' : 'text-red-900' }}">
                            Consenso Privacy Policy
                        </h4>
                        <p class="text-sm {{ $clienti_senza_privacy->count() == 0 ? 'text-green-700' : 'text-red-700' }} mt-1">
                            @if($clienti_senza_privacy->count() == 0)
                                Tutti i clienti hanno fornito il consenso alla Privacy Policy
                            @else
                                {{ $clienti_senza_privacy->count() }} clienti senza Privacy Policy attiva
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Check 2: Consensi Scaduti -->
                <div class="flex items-start p-4 rounded-lg {{ $consensi_scaduti->count() == 0 ? 'bg-green-50 border-l-4 border-green-500' : 'bg-yellow-50 border-l-4 border-yellow-500' }}">
                    <div class="flex-shrink-0">
                        @if($consensi_scaduti->count() == 0)
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        @else
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                        @endif
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold {{ $consensi_scaduti->count() == 0 ? 'text-green-900' : 'text-yellow-900' }}">
                            Validità Consensi
                        </h4>
                        <p class="text-sm {{ $consensi_scaduti->count() == 0 ? 'text-green-700' : 'text-yellow-700' }} mt-1">
                            @if($consensi_scaduti->count() == 0)
                                Tutti i consensi sono validi (meno di 2 anni)
                            @else
                                {{ $consensi_scaduti->count() }} consensi oltre i 2 anni richiedono rinnovo
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Check 3: Tracciamento IP e User Agent -->
                <div class="flex items-start p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-900">
                            Tracciamento Consensi
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            Sistema traccia IP, User Agent e timestamp per tutti i consensi
                        </p>
                    </div>
                </div>

                <!-- Check 4: Versioning Policy -->
                <div class="flex items-start p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-900">
                            Versioning Privacy Policy
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            Sistema mantiene storico versioni policy e data accettazione
                        </p>
                    </div>
                </div>

                <!-- Check 5: Gestione Revoche -->
                <div class="flex items-start p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-900">
                            Gestione Revoche
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            Sistema permette revoca consensi e traccia data revoca
                        </p>
                    </div>
                </div>

                <!-- Check 6: Export Dati -->
                <div class="flex items-start p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-900">
                            Right to Data Portability
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            Clienti possono esportare i propri dati personali (Art. 20 GDPR)
                        </p>
                    </div>
                </div>

                <!-- Check 7: Right to be Forgotten -->
                <div class="flex items-start p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-green-900">
                            Right to be Forgotten
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            Clienti possono richiedere cancellazione dati (Art. 17 GDPR)
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Azioni Rapide -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-bolt mr-2"></i>
            Azioni Rapide
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.privacy.index') }}"
               class="flex items-center p-4 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-shield-alt text-2xl mr-4"></i>
                <div>
                    <p class="font-semibold">Dashboard Privacy</p>
                    <p class="text-sm opacity-90">Visualizza tutti i consensi</p>
                </div>
            </a>
            <a href="{{ route('admin.privacy.export') }}"
               class="flex items-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-file-csv text-2xl mr-4"></i>
                <div>
                    <p class="font-semibold">Esporta Consensi</p>
                    <p class="text-sm opacity-90">Download CSV completo</p>
                </div>
            </a>
            <a href="{{ route('admin.clienti.index') }}"
               class="flex items-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-users text-2xl mr-4"></i>
                <div>
                    <p class="font-semibold">Gestione Clienti</p>
                    <p class="text-sm opacity-90">Anagrafica completa</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
