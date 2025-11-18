@extends('layouts.professionista')

@section('titolo', 'Storico Pagamenti')
@section('sottotitolo', 'Visualizza lo storico dei tuoi pagamenti')

@section('content')
<div class="space-y-6">

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-blue-900">Storico Pagamenti in Fase di Sviluppo</h3>
                <p class="text-blue-700 mt-2">
                    La funzionalità di storico pagamenti è attualmente in fase di sviluppo.
                    Al momento puoi visualizzare i tuoi compensi maturati nella sezione "Compensi".
                </p>
                <p class="text-blue-700 mt-2">
                    Per informazioni sui pagamenti effettuati, contatta l'amministrazione.
                </p>
            </div>
        </div>
    </div>

    <!-- Placeholder Tabella -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Storico Pagamenti</h3>
        </div>
        <div class="p-12 text-center">
            <i class="fas fa-file-invoice-dollar text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Funzionalità in arrivo</p>
            <p class="text-gray-400 text-sm mt-2">
                Presto potrai visualizzare qui lo storico completo dei tuoi pagamenti
            </p>
        </div>
    </div>

    <!-- Link utili -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('professionista.compensi.index') }}"
           class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-euro-sign text-3xl text-violet-500"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">Visualizza Compensi</h4>
                    <p class="text-sm text-gray-600 mt-1">Vedi i compensi maturati</p>
                </div>
            </div>
        </a>

        <a href="{{ route('professionista.lezioni.index') }}"
           class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-list text-3xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">Agenda Lezioni</h4>
                    <p class="text-sm text-gray-600 mt-1">Vedi le tue lezioni</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
