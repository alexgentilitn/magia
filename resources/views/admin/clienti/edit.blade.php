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