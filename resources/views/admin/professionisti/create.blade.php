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

    <form method="POST" action="{{ route('admin.professionisti.store') }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Telefono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile</label>
                <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

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

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="pending" {{ old('stato') == 'pending' ? 'selected' : '' }}>In Attesa di Approvazione</option>
                    <option value="attivo" {{ old('stato') == 'attivo' ? 'selected' : '' }}>Attivo</option>
                    <option value="sospeso" {{ old('stato') == 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                    <option value="inattivo" {{ old('stato') == 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                </select>
            </div>

            <!-- Bio -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Biografia Professionale</label>
                <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Descrizione delle competenze e dell'esperienza...">{{ old('bio') }}</textarea>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Nota:</strong> Verrà creato un account professionista con accesso limitato all'area amministrativa.
                Il professionista riceverà un'email con le credenziali di accesso temporanee valide per 24 ore.
                Dovrà cambiarle al primo accesso per motivi di sicurezza.
            </p>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Crea Professionista
            </button>
        </div>
    </form>
</div>
@endsection
