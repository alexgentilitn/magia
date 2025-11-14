<form id="formCreaLezione" class="space-y-4">
    @csrf

    <!-- Informazioni Base -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Titolo -->
        <div class="md:col-span-2">
            <label for="titolo" class="block text-sm font-medium text-gray-700 mb-2">
                Titolo Lezione <span class="text-red-500">*</span>
            </label>
            <input type="text" id="titolo" name="titolo" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
        </div>

        <!-- Data -->
        <div>
            <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                Data <span class="text-red-500">*</span>
            </label>
            <input type="date" id="data" name="data" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
        </div>

        <!-- Tipologia -->
        <div>
            <label for="tipologia" class="block text-sm font-medium text-gray-700 mb-2">
                Tipologia <span class="text-red-500">*</span>
            </label>
            <select id="tipologia" name="tipologia" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <option value="gruppo">Gruppo</option>
                <option value="individuale">Individuale</option>
                <option value="online">Online</option>
                <option value="ibrida">Ibrida</option>
            </select>
        </div>

        <!-- Ora Inizio -->
        <div>
            <label for="ora_inizio" class="block text-sm font-medium text-gray-700 mb-2">
                Ora Inizio <span class="text-red-500">*</span>
            </label>
            <input type="time" id="ora_inizio" name="ora_inizio" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
        </div>

        <!-- Ora Fine -->
        <div>
            <label for="ora_fine" class="block text-sm font-medium text-gray-700 mb-2">
                Ora Fine <span class="text-red-500">*</span>
            </label>
            <input type="time" id="ora_fine" name="ora_fine" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
        </div>

        <!-- Professionista -->
        <div>
            <label for="professionista_id" class="block text-sm font-medium text-gray-700 mb-2">
                Professionista <span class="text-red-500">*</span>
            </label>
            <select id="professionista_id" name="professionista_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <option value="">Seleziona...</option>
                @foreach($professionisti as $prof)
                    <option value="{{ $prof->utente_id }}">{{ $prof->nome }} {{ $prof->cognome }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sede -->
        <div>
            <label for="sede_id" class="block text-sm font-medium text-gray-700 mb-2">
                Sede <span class="text-red-500">*</span>
            </label>
            <select id="sede_id" name="sede_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <option value="">Seleziona...</option>
                @foreach($sedi as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                @endforeach
            </select>
        </div>

        <!-- Posti Totali -->
        <div>
            <label for="posti_totali" class="block text-sm font-medium text-gray-700 mb-2">
                Posti Totali <span class="text-red-500">*</span>
            </label>
            <input type="number" id="posti_totali" name="posti_totali" min="1" value="10" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
        </div>

        <!-- Stato -->
        <div>
            <label for="stato" class="block text-sm font-medium text-gray-700 mb-2">
                Stato <span class="text-red-500">*</span>
            </label>
            <select id="stato" name="stato" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <option value="programmata">Programmata</option>
                <option value="confermata">Confermata</option>
            </select>
        </div>

        <!-- Descrizione -->
        <div class="md:col-span-2">
            <label for="descrizione" class="block text-sm font-medium text-gray-700 mb-2">
                Descrizione
            </label>
            <textarea id="descrizione" name="descrizione" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"></textarea>
        </div>
    </div>

    <!-- Messaggio Errore -->
    <div id="erroreCreaLezione" class="hidden p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <p class="font-medium">Si è verificato un errore:</p>
        <p id="messaggioErrore" class="text-sm mt-1"></p>
    </div>

    <!-- Azioni -->
    <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <button type="button" onclick="closeModalCrea()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-times mr-2"></i>
            Annulla
        </button>
        <button type="submit"
                class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition-colors">
            <i class="fas fa-save mr-2"></i>
            Crea Lezione
        </button>
    </div>
</form>
