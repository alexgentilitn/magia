@extends('layouts.admin')

@section('titolo', 'Modifica Cliente')

@section('contenuto')
<div class="p-6">
    
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.clienti.show', $cliente->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Modifica Cliente: {{ $cliente->nomeCompleto }}</h2>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.clienti.update', $cliente->id) }}" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Sezione Anagrafica -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-id-card text-fucsia-magia mr-2"></i> Dati Anagrafici
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                        <input type="text" name="cognome" value="{{ old('cognome', $cliente->cognome) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('cognome') border-red-500 @enderror">
                        @error('cognome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $cliente->email) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codice Fiscale *</label>
                        <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $cliente->codice_fiscale) }}" required maxlength="16"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('codice_fiscale') border-red-500 @enderror">
                        @error('codice_fiscale')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita</label>
                        <input type="date" name="data_nascita" value="{{ old('data_nascita', $cliente->data_nascita?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sesso</label>
                        <select name="sesso" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Seleziona...</option>
                            <option value="F" {{ old('sesso', $cliente->sesso) === 'F' ? 'selected' : '' }}>Femmina</option>
                            <option value="M" {{ old('sesso', $cliente->sesso) === 'M' ? 'selected' : '' }}>Maschio</option>
                            <option value="Altro" {{ old('sesso', $cliente->sesso) === 'Altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile</label>
                        <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile', $cliente->telefono_mobile) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Fisso</label>
                        <input type="text" name="telefono_fisso" value="{{ old('telefono_fisso', $cliente->telefono_fisso) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- NUOVA SEZIONE: SICUREZZA E PASSWORD -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-lock text-fucsia-magia mr-2"></i> Sicurezza e Accesso
                </h3>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Compila questi campi solo se vuoi cambiare la password. Lascia vuoti per mantenere la password attuale.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nuova Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('password') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Minimo 8 caratteri</p>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conferma Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <p class="text-xs text-gray-500 mt-1">Ripeti la nuova password</p>
                    </div>
                </div>
            </div>

            <!-- Sezione Indirizzo -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-map-marker-alt text-fucsia-magia mr-2"></i> Indirizzo
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Via</label>
                        <input type="text" name="indirizzo_via" value="{{ old('indirizzo_via', $cliente->indirizzo_via) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Città</label>
                        <input type="text" name="indirizzo_citta" value="{{ old('indirizzo_citta', $cliente->indirizzo_citta) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CAP</label>
                        <input type="text" name="indirizzo_cap" value="{{ old('indirizzo_cap', $cliente->indirizzo_cap) }}" maxlength="5"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                        <input type="text" name="indirizzo_provincia" value="{{ old('indirizzo_provincia', $cliente->indirizzo_provincia) }}" maxlength="2"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- Sezione Obiettivi e Attività -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-bullseye text-fucsia-magia mr-2"></i> Obiettivi e Livello di Attività
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Obiettivi Personali</label>
                        <textarea name="obiettivi_personali" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Es: Perdere peso, tonificare, aumentare energia...">{{ old('obiettivi_personali', $cliente->obiettivi_personali) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Livello di Attività</label>
                        <select name="livello_attivita" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Seleziona...</option>
                            <option value="sedentario" {{ old('livello_attivita', $cliente->livello_attivita) === 'sedentario' ? 'selected' : '' }}>Sedentario</option>
                            <option value="leggero" {{ old('livello_attivita', $cliente->livello_attivita) === 'leggero' ? 'selected' : '' }}>Leggero</option>
                            <option value="moderato" {{ old('livello_attivita', $cliente->livello_attivita) === 'moderato' ? 'selected' : '' }}>Moderato</option>
                            <option value="intenso" {{ old('livello_attivita', $cliente->livello_attivita) === 'intenso' ? 'selected' : '' }}>Intenso</option>
                            <option value="molto_intenso" {{ old('livello_attivita', $cliente->livello_attivita) === 'molto_intenso' ? 'selected' : '' }}>Molto Intenso</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sezione Dati Medici -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-heartbeat text-fucsia-magia mr-2"></i> Dati Medici e Sanitari
                </h3>
                <div class="bg-pink-50 border-l-4 border-pink-500 p-4 mb-4">
                    <p class="text-sm text-pink-700">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Questi dati sono trattati con il massimo riserbo secondo il GDPR (dati sensibili relativi alla salute)
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Mediche</label>
                        <textarea name="note_mediche" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Eventuali condizioni mediche da considerare...">{{ old('note_mediche', $cliente->note_mediche) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allergie e Intolleranze</label>
                        <textarea name="allergie_intolleranze" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Es: lattosio, glutine, nichel...">{{ old('allergie_intolleranze', $cliente->allergie_intolleranze) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patologie</label>
                        <textarea name="patologie" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Eventuali patologie diagnosticate...">{{ old('patologie', $cliente->patologie) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Farmaci Assunti</label>
                        <textarea name="farmaci_assunti" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Farmaci assunti regolarmente...">{{ old('farmaci_assunti', $cliente->farmaci_assunti) }}</textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="certificato_medico_presente" value="1"
                                   {{ old('certificato_medico_presente', $cliente->certificato_medico_presente) ? 'checked' : '' }}
                                   class="h-5 w-5 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">Certificato Medico Presente</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sezione Alimentazione -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-utensils text-fucsia-magia mr-2"></i> Alimentazione e Preferenze
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Regime Alimentare</label>
                        <select name="regime_alimentare" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Seleziona...</option>
                            <option value="onnivoro" {{ old('regime_alimentare', $cliente->regime_alimentare) === 'onnivoro' ? 'selected' : '' }}>Onnivoro</option>
                            <option value="vegetariano" {{ old('regime_alimentare', $cliente->regime_alimentare) === 'vegetariano' ? 'selected' : '' }}>Vegetariano</option>
                            <option value="vegano" {{ old('regime_alimentare', $cliente->regime_alimentare) === 'vegano' ? 'selected' : '' }}>Vegano</option>
                            <option value="pescetariano" {{ old('regime_alimentare', $cliente->regime_alimentare) === 'pescetariano' ? 'selected' : '' }}>Pescetariano</option>
                            <option value="altro" {{ old('regime_alimentare', $cliente->regime_alimentare) === 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferenze Alimentari</label>
                        <input type="text" name="preferenze_alimentari" value="{{ old('preferenze_alimentari', $cliente->preferenze_alimentari) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="Es: preferisce pesce a carne...">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cibi da Evitare</label>
                        <textarea name="cibi_da_evitare" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Cibi che non gradisce o deve evitare...">{{ old('cibi_da_evitare', $cliente->cibi_da_evitare) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sezione Parametri Corporei -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-weight text-fucsia-magia mr-2"></i> Parametri Corporei
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso (kg)</label>
                        <input type="number" step="0.01" name="peso" value="{{ old('peso', $cliente->peso) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="65.50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Altezza (cm)</label>
                        <input type="number" step="0.01" name="altezza" value="{{ old('altezza', $cliente->altezza) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="165.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">BMI (calcolato auto)</label>
                        <input type="number" step="0.01" name="bmi" value="{{ old('bmi', $cliente->bmi) }}"
                               class="w-full px-4 py-2 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="Calcolato automaticamente" readonly>
                        <p class="text-xs text-gray-500 mt-1">Si calcola da peso/altezza²</p>
                    </div>
                </div>
            </div>

            <!-- Sezione Circonferenze -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-ruler text-fucsia-magia mr-2"></i> Circonferenze Corporee (cm)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vita</label>
                        <input type="number" step="0.01" name="circonferenza_vita" value="{{ old('circonferenza_vita', $cliente->circonferenza_vita) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fianchi</label>
                        <input type="number" step="0.01" name="circonferenza_fianchi" value="{{ old('circonferenza_fianchi', $cliente->circonferenza_fianchi) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Petto</label>
                        <input type="number" step="0.01" name="circonferenza_petto" value="{{ old('circonferenza_petto', $cliente->circonferenza_petto) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Braccio Destro</label>
                        <input type="number" step="0.01" name="circonferenza_braccio_dx" value="{{ old('circonferenza_braccio_dx', $cliente->circonferenza_braccio_dx) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Braccio Sinistro</label>
                        <input type="number" step="0.01" name="circonferenza_braccio_sx" value="{{ old('circonferenza_braccio_sx', $cliente->circonferenza_braccio_sx) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Coscia Destra</label>
                        <input type="number" step="0.01" name="circonferenza_coscia_dx" value="{{ old('circonferenza_coscia_dx', $cliente->circonferenza_coscia_dx) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Coscia Sinistra</label>
                        <input type="number" step="0.01" name="circonferenza_coscia_sx" value="{{ old('circonferenza_coscia_sx', $cliente->circonferenza_coscia_sx) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- Sezione Timestamp Misurazioni -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-clock text-fucsia-magia mr-2"></i> Ultime Misurazioni
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ultima Pesata</label>
                        <input type="datetime-local" name="ultima_pesata" value="{{ old('ultima_pesata', $cliente->ultima_pesata?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ultima Misurazione</label>
                        <input type="datetime-local" name="ultima_misurazione" value="{{ old('ultima_misurazione', $cliente->ultima_misurazione?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- Sezione Note Professionista -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-user-md text-fucsia-magia mr-2"></i> Note Professionista
                </h3>
                <textarea name="note_professionista" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia" placeholder="Note riservate per l'uso del professionista (non visibili alla cliente)...">{{ old('note_professionista', $cliente->note_professionista) }}</textarea>
            </div>

            <!-- Sezione Programma -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-dumbbell text-fucsia-magia mr-2"></i> Programma e Stato
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Programma</label>
                        <select name="programma_attuale" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Nessuno</option>
                            <option value="balla_snella" {{ $cliente->programma_attuale === 'balla_snella' ? 'selected' : '' }}>Balla & Snella</option>
                            <option value="offerta_magia" {{ $cliente->programma_attuale === 'offerta_magia' ? 'selected' : '' }}>Offerta MA.GIA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato Cliente</label>
                        <select name="stato_cliente" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="attivo" {{ $cliente->stato_cliente === 'attivo' ? 'selected' : '' }}>Attivo</option>
                            <option value="sospeso" {{ $cliente->stato_cliente === 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                            <option value="inattivo" {{ $cliente->stato_cliente === 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i> Note Interne
                </h3>
                <textarea name="note_interne" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">{{ old('note_interne', $cliente->note_interne) }}</textarea>
            </div>

            <!-- Bottoni -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.clienti.show', $cliente->id) }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Salva Modifiche
                </button>
            </div>

        </form>

    </div>

</div>
@endsection