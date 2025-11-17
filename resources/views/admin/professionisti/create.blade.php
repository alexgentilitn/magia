@extends('layouts.admin')

@section('titolo', 'Nuovo Professionista')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Nuovo Professionista</h1>
    </div>

    <!-- Mostra errori di validazione -->
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
            <div>
                <h3 class="text-red-800 font-semibold mb-2">Errori di validazione:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.professionisti.store') }}" class="space-y-6">
        @csrf

        <!-- SEZIONE 1: Dati Anagrafici -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-user mr-2 text-fucsia-magia"></i> Dati Anagrafici
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                    <input type="text" name="nome" required value="{{ old('nome') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Cognome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                    <input type="text" name="cognome" required value="{{ old('cognome') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Codice Fiscale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Codice Fiscale</label>
                    <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" maxlength="16" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent uppercase" placeholder="RSSMRA80A01H501U">
                </div>

                <!-- Data Nascita -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita</label>
                    <input type="date" name="data_nascita" value="{{ old('data_nascita') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Sesso -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sesso</label>
                    <select name="sesso" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Seleziona...</option>
                        <option value="F" {{ old('sesso') == 'F' ? 'selected' : '' }}>Femminile</option>
                        <option value="M" {{ old('sesso') == 'M' ? 'selected' : '' }}>Maschile</option>
                        <option value="Altro" {{ old('sesso') == 'Altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SEZIONE 2: Contatti e Indirizzo -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-address-book mr-2 text-fucsia-magia"></i> Contatti e Indirizzo
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Telefono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile</label>
                    <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="+39 333 1234567">
                </div>

                <!-- Indirizzo -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Indirizzo</label>
                    <input type="text" name="indirizzo" value="{{ old('indirizzo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Via, Piazza, Viale e numero civico">
                </div>

                <!-- Città -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Città</label>
                    <input type="text" name="citta" value="{{ old('citta') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Provincia -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia') }}" maxlength="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent uppercase" placeholder="RM">
                </div>

                <!-- CAP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CAP</label>
                    <input type="text" name="cap" value="{{ old('cap') }}" maxlength="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="00100">
                </div>
            </div>
        </div>

        <!-- SEZIONE 3: Dati Professionali -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-briefcase mr-2 text-fucsia-magia"></i> Dati Professionali
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Titolo Professionale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titolo Professionale</label>
                    <input type="text" name="titolo_professionale" value="{{ old('titolo_professionale') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Personal Trainer, Nutrizionista">
                </div>

                <!-- Anni Esperienza -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anni di Esperienza</label>
                    <input type="number" name="anni_esperienza" min="0" value="{{ old('anni_esperienza') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Partita IVA -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Partita IVA</label>
                    <input type="text" name="partita_iva" value="{{ old('partita_iva') }}" maxlength="20" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="IT12345678901">
                </div>

                <!-- Tipo Contratto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Contratto</label>
                    <select name="tipo_contratto" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Seleziona...</option>
                        <option value="dipendente" {{ old('tipo_contratto') == 'dipendente' ? 'selected' : '' }}>Dipendente</option>
                        <option value="collaboratore" {{ old('tipo_contratto') == 'collaboratore' ? 'selected' : '' }}>Collaboratore</option>
                        <option value="freelance" {{ old('tipo_contratto') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="altro" {{ old('tipo_contratto') == 'altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>

                <!-- Data Assunzione -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Assunzione/Inizio Collaborazione</label>
                    <input type="date" name="data_assunzione" value="{{ old('data_assunzione') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Bio -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Biografia Professionale</label>
                    <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Descrizione delle competenze e dell'esperienza...">{{ old('bio') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SEZIONE 4: Tariffe -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-euro-sign mr-2 text-fucsia-magia"></i> Tariffe
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tariffa Oraria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Oraria (€)</label>
                    <input type="number" name="tariffa_oraria" min="0" step="0.01" value="{{ old('tariffa_oraria') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Tariffa Lezione Gruppo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Gruppo (€)</label>
                    <input type="number" name="tariffa_lezione_gruppo" min="0" step="0.01" value="{{ old('tariffa_lezione_gruppo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Tariffa Lezione Privata -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Privata (€)</label>
                    <input type="number" name="tariffa_lezione_privata" min="0" step="0.01" value="{{ old('tariffa_lezione_privata') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- SEZIONE 5: Disponibilità Temporale -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-calendar-alt mr-2 text-fucsia-magia"></i> Disponibilità Temporale
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Disponibile Da -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disponibile Da</label>
                    <input type="date" name="disponibile_da" value="{{ old('disponibile_da') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <!-- Disponibile Fino -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disponibile Fino</label>
                    <input type="date" name="disponibile_fino" value="{{ old('disponibile_fino') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Lascia vuoto se disponibilità a tempo indeterminato</p>
                </div>
            </div>
        </div>

        <!-- SEZIONE 6: Social Media e Web -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-share-alt mr-2 text-fucsia-magia"></i> Profili Online e Social Media
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sito Web -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-globe mr-1"></i> Sito Web
                    </label>
                    <input type="url" name="sito_web" value="{{ old('sito_web') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="https://www.miosito.it">
                </div>

                <!-- Instagram -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-instagram mr-1"></i> Instagram
                    </label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="@username">
                </div>

                <!-- Facebook -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-facebook mr-1"></i> Facebook
                    </label>
                    <input type="url" name="facebook" value="{{ old('facebook') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="https://facebook.com/username">
                </div>

                <!-- LinkedIn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-linkedin mr-1"></i> LinkedIn
                    </label>
                    <input type="url" name="linkedin" value="{{ old('linkedin') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="https://linkedin.com/in/username">
                </div>

                <!-- TikTok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-tiktok mr-1"></i> TikTok
                    </label>
                    <input type="text" name="tiktok" value="{{ old('tiktok') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="@username">
                </div>

                <!-- Video Presentazione -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-youtube mr-1"></i> Video Presentazione (URL)
                    </label>
                    <input type="url" name="video_presentazione" value="{{ old('video_presentazione') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="https://youtube.com/watch?v=...">
                    <p class="text-xs text-gray-500 mt-1">Link YouTube o Vimeo</p>
                </div>
            </div>
        </div>

        <!-- SEZIONE 7: Stato e Visibilità -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-cog mr-2 text-fucsia-magia"></i> Stato e Configurazioni
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Stato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                    <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="pending" {{ old('stato', 'pending') == 'pending' ? 'selected' : '' }}>In Attesa di Approvazione</option>
                        <option value="attivo" {{ old('stato') == 'attivo' ? 'selected' : '' }}>Attivo</option>
                        <option value="sospeso" {{ old('stato') == 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                        <option value="inattivo" {{ old('stato') == 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                    </select>
                </div>

                <!-- Note Interne -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note Interne (riservate admin)</label>
                    <textarea name="note_interne" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Note visibili solo agli amministratori...">{{ old('note_interne') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Nota:</strong> Verrà creato un account professionista con accesso all'area riservata.
                Il professionista riceverà un'email con le credenziali di accesso temporanee valide per 24 ore.
                Dovrà cambiarle al primo accesso per motivi di sicurezza.
            </p>
        </div>

        <!-- Bottoni Azioni -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i> Annulla
            </a>
            <button type="submit" class="px-6 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Crea Professionista
            </button>
        </div>
    </form>
</div>
@endsection
