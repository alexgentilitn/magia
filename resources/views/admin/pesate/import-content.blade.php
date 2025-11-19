<!-- Info Box -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
    <h3 class="font-bold text-blue-900 mb-3 flex items-center">
        <i class="fas fa-info-circle mr-2"></i>
        Come funziona l'importazione
    </h3>
    <ol class="list-decimal list-inside space-y-2 text-blue-800">
        <li>Seleziona la <strong>sede</strong> relativa alle pesate da importare</li>
        <li>Carica il <strong>file Excel</strong> con i dati delle pesate</li>
        <li>Visualizza l'<strong>anteprima</strong> dei dati che verranno importati</li>
        <li>Conferma l'importazione - solo le righe valide verranno inserite</li>
    </ol>

    <div class="mt-4 p-4 bg-white rounded border border-blue-200">
        <h4 class="font-bold text-blue-900 mb-2">
            <i class="fas fa-file-excel mr-2"></i>
            Formato File Excel Richiesto
        </h4>
        <p class="text-sm text-blue-800 mb-2">
            Il file deve avere le colonne nell'ordine esatto con <strong>header alla riga 2</strong> e <strong>dati dalla riga 3</strong>:
        </p>
        <div class="bg-gray-100 p-3 rounded font-mono text-xs overflow-x-auto">
            A: Cognome* | B: Nome* | C: Peso (kg)* | D: BMI | E: Peso senza grassi | F: Muscolo scheletrico (%) |
            G: Grasso corporeo (%) | H: Grasso sottocutaneo (%) | I: Grasso viscerale | J: Acqua corporea (%) |
            K: Massa muscolare (kg) | L: Massa ossea (kg) | M: Proteine (%) | N: BMR (kcal) | O: Età metabolica |
            P: Data rilevazione*
        </div>
        <p class="text-sm text-blue-700 mt-2">
            <strong>*</strong> = Campi obbligatori
        </p>
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
                <input type="file" name="file" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-fucsia-magia file:text-white
                              hover:file:bg-viola-magia
                              cursor-pointer">
                <p class="text-sm text-gray-500 mt-2">
                    Formati supportati: .xlsx, .xls (max 10 MB)
                </p>
            </div>
        </div>

        <!-- Bottoni -->
        <div class="flex gap-4">
            <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-upload mr-2"></i>
                Carica e Visualizza Anteprima
            </button>
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
                <li>I clienti devono essere già presenti nel database (usa Nome e Cognome per l'abbinamento)</li>
                <li>Le pesate duplicate (stesso cliente, stessa data) non verranno importate</li>
                <li>Verifica sempre l'anteprima prima di confermare l'importazione</li>
                <li>Il processo è reversibile solo eliminando manualmente le pesate importate</li>
            </ul>
        </div>
    </div>
</div>
