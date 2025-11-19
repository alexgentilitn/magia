@extends('layouts.admin')

@section('titolo', 'Importa Pesate da Excel')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Importa Pesate da Excel</h2>
            <p class="text-gray-600 mt-1">Carica un file Excel con i dati delle pesate clienti</p>
        </div>
        <a href="{{ route('admin.clienti-pesate.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Torna alla Lista
        </a>
    </div>

    <!-- Messaggi di errore -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <p class="font-medium">Errori di validazione:</p>
            </div>
            <ul class="list-disc list-inside ml-8">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
        <h3 class="font-bold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Come funziona l'importazione
        </h3>
        <ol class="list-decimal list-inside space-y-2 text-blue-800">
            <li>Seleziona la <strong>sede</strong> relativa alle pesate da importare</li>
            <li>Carica il <strong>file Excel o CSV</strong> con i dati delle pesate</li>
            <li><strong>Mappa le colonne</strong> del tuo file ai campi del database</li>
            <li>Visualizza l'<strong>anteprima</strong> dei dati che verranno importati</li>
            <li>Conferma l'importazione - solo le righe valide verranno inserite</li>
        </ol>

        <div class="mt-4 p-4 bg-white rounded border border-blue-200">
            <h4 class="font-bold text-blue-900 mb-2 flex items-center">
                <i class="fas fa-file-excel mr-2"></i>
                Requisiti File
            </h4>
            <ul class="list-disc list-inside space-y-2 text-blue-800 text-sm">
                <li>Il file può essere in formato <strong>Excel (.xlsx, .xls)</strong> o <strong>CSV</strong></li>
                <li>Le colonne possono essere in <strong>qualsiasi ordine</strong> - verranno mappate nel passaggio successivo</li>
                <li>I <strong>campi obbligatori</strong> sono: <strong>Nome</strong>, <strong>Cognome</strong>, <strong>Peso</strong></li>
                <li>La <strong>Data Rilevazione</strong> verrà impostata <strong>automaticamente a OGGI</strong> ({{ date('d/m/Y') }})</li>
                <li>Se il <strong>cliente non esiste</strong>, verrà <strong>creato automaticamente</strong></li>
                <li>Il <strong>Codice Fiscale</strong> è opzionale ma <strong>raccomandato</strong> per identificazione più precisa</li>
            </ul>
        </div>
    </div>

    <!-- Form Upload -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('pesate.process-import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Selezione Sede -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-1 text-fucsia-magia"></i>
                    Sede <span class="text-red-500">*</span>
                </label>
                <select name="sede" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="">Seleziona una sede...</option>
                    <option value="Calliano">Calliano</option>
                    <option value="Darè">Darè</option>
                    <option value="Pieve di Bono">Pieve di Bono</option>
                    <option value="Riva">Riva</option>
                    <option value="Trento">Trento</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Seleziona la sede dove sono state effettuate le pesate</p>
            </div>

            <!-- Upload File -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file-excel mr-1 text-green-600"></i>
                    File Excel <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-fucsia-magia transition">
                    <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-fucsia-magia file:text-white
                                  hover:file:bg-viola-magia
                                  cursor-pointer">
                    <p class="text-sm text-gray-500 mt-2">
                        Formati supportati: .xlsx, .xls, .csv (max 10 MB)
                    </p>
                </div>
            </div>

            <!-- Bottoni -->
            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-upload mr-2"></i>
                    Carica File e Mappa Colonne
                </button>
                <a href="{{ route('admin.clienti-pesate.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
            </div>
        </form>
    </div>

    <!-- Note Importanti -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-6 rounded-lg">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
            <div>
                <p class="font-bold text-yellow-900 mb-1">Note Importanti:</p>
                <ul class="list-disc list-inside text-yellow-800 text-sm space-y-1">
                    <li><strong>Nuova funzionalità:</strong> I clienti che non esistono verranno creati automaticamente durante l'importazione</li>
                    <li>Il sistema cercherà i clienti per <strong>Nome + Cognome</strong> oppure per <strong>Codice Fiscale</strong></li>
                    <li>Verifica sempre l'anteprima prima di confermare - i nuovi clienti saranno evidenziati</li>
                    <li>Le pesate duplicate (stesso cliente, stessa data) potrebbero causare errori</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
