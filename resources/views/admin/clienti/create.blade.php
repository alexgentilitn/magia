@extends('layouts.admin')

@section('titolo', 'Nuova Cliente')

@section('contenuto')
<div class="p-6">
    
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.clienti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Aggiungi Nuova Cliente</h2>
            <p class="text-gray-600 mt-1">Compila tutti i campi obbligatori</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.clienti.store') }}" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Sezione Account -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-user-circle text-fucsia-magia mr-2"></i> Account
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Anagrafica -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-id-card text-fucsia-magia mr-2"></i> Dati Anagrafici
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                        <input type="text" name="cognome" value="{{ old('cognome') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('cognome') border-red-500 @enderror">
                        @error('cognome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codice Fiscale *</label>
                        <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" required maxlength="16"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('codice_fiscale') border-red-500 @enderror">
                        @error('codice_fiscale')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita *</label>
                        <input type="date" name="data_nascita" value="{{ old('data_nascita') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('data_nascita') border-red-500 @enderror">
                        @error('data_nascita')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile *</label>
                        <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('telefono_mobile') border-red-500 @enderror">
                        @error('telefono_mobile')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Fisso</label>
                        <input type="text" name="telefono_fisso" value="{{ old('telefono_fisso') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Via *</label>
                        <input type="text" name="indirizzo_via" value="{{ old('indirizzo_via') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Città *</label>
                        <input type="text" name="indirizzo_citta" value="{{ old('indirizzo_citta') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CAP *</label>
                        <input type="text" name="indirizzo_cap" value="{{ old('indirizzo_cap') }}" required maxlength="5"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provincia *</label>
                        <input type="text" name="indirizzo_provincia" value="{{ old('indirizzo_provincia') }}" required maxlength="2"
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
                            <option value="balla_snella">Balla & Snella</option>
                            <option value="offerta_magia">Offerta MA.GIA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato Cliente</label>
                        <select name="stato_cliente" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="attivo">Attivo</option>
                            <option value="sospeso">Sospeso</option>
                            <option value="inattivo">Inattivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bottoni -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.clienti.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Salva Cliente
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
