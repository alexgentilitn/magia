@extends('layouts.pubblico')

@section('titolo', 'Registrazione')

@section('contenuto')
<div class="min-h-screen bg-gradient-to-br from-viola-magia via-fucsia-magia to-arancio-magia py-12 px-4">
    <div class="max-w-3xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">Inizia Il Tuo Percorso</h1>
            <p class="text-xl text-pink-100">Compila il form per iscriverti gratuitamente</p>
        </div>

        <!-- Form Registrazione -->
        <form method="POST" action="{{ route('registrazione.store') }}" class="bg-white rounded-2xl shadow-2xl p-8" x-data="{ sezione: 1 }">
            @csrf

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" :class="sezione >= 1 ? 'text-fucsia-magia' : 'text-gray-400'">Account</span>
                    <span class="text-sm font-medium" :class="sezione >= 2 ? 'text-fucsia-magia' : 'text-gray-400'">Anagrafica</span>
                    <span class="text-sm font-medium" :class="sezione >= 3 ? 'text-fucsia-magia' : 'text-gray-400'">Indirizzo</span>
                    <span class="text-sm font-medium" :class="sezione >= 4 ? 'text-fucsia-magia' : 'text-gray-400'">Privacy</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full">
                    <div class="h-2 bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-full transition-all duration-300" 
                         :style="'width: ' + (sezione * 25) + '%'"></div>
                </div>
            </div>

            <!-- Sezione 1: Account -->
            <div x-show="sezione === 1" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Crea il tuo Account</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div x-data="{ mostra: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <div class="relative">
                            <input :type="mostra ? 'text' : 'password'" name="password" required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('password') border-red-500 @enderror">
                            <button type="button" @click="mostra = !mostra" class="absolute right-3 top-3 text-gray-400">
                                <i :class="mostra ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conferma Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codice Invito (opzionale)</label>
                        <input type="text" name="codice_invito" value="{{ old('codice_invito') }}"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <p class="text-sm text-gray-500 mt-1">Se sei stata invitata da un'amica, inserisci il suo codice</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="sezione = 2" 
                            class="px-8 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg font-semibold hover:shadow-lg transition">
                        Avanti <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Sezione 2: Anagrafica -->
            <div x-show="sezione === 2" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Dati Personali</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                        <input type="text" name="cognome" value="{{ old('cognome') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('cognome') border-red-500 @enderror">
                        @error('cognome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codice Fiscale *</label>
                        <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" required maxlength="16"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('codice_fiscale') border-red-500 @enderror">
                        @error('codice_fiscale')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita *</label>
                        <input type="date" name="data_nascita" value="{{ old('data_nascita') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('data_nascita') border-red-500 @enderror">
                        @error('data_nascita')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile *</label>
                        <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('telefono_mobile') border-red-500 @enderror">
                        @error('telefono_mobile')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Fisso</label>
                        <input type="text" name="telefono_fisso" value="{{ old('telefono_fisso') }}"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
                <div class="mt-6 flex justify-between">
                    <button type="button" @click="sezione = 1" 
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Indietro
                    </button>
                    <button type="button" @click="sezione = 3" 
                            class="px-8 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg font-semibold hover:shadow-lg transition">
                        Avanti <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Sezione 3: Indirizzo -->
            <div x-show="sezione === 3" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Indirizzo di Residenza</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Via *</label>
                        <input type="text" name="indirizzo_via" value="{{ old('indirizzo_via') }}" required
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('indirizzo_via') border-red-500 @enderror">
                        @error('indirizzo_via')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Città *</label>
                            <input type="text" name="indirizzo_citta" value="{{ old('indirizzo_citta') }}" required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CAP *</label>
                            <input type="text" name="indirizzo_cap" value="{{ old('indirizzo_cap') }}" required maxlength="5"
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Provincia *</label>
                            <input type="text" name="indirizzo_provincia" value="{{ old('indirizzo_provincia') }}" required maxlength="2"
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-between">
                    <button type="button" @click="sezione = 2" 
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Indietro
                    </button>
                    <button type="button" @click="sezione = 4" 
                            class="px-8 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg font-semibold hover:shadow-lg transition">
                        Avanti <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Sezione 4: Privacy -->
            <div x-show="sezione === 4" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-shield-alt text-fucsia-magia mr-2"></i>
                    Privacy e Consensi
                </h3>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Per completare la registrazione, è necessario leggere e accettare i documenti seguenti.
                        Alcuni consensi sono obbligatori, altri facoltativi.
                    </p>
                </div>

                <div class="space-y-5">
                    <!-- Privacy Policy - Obbligatorio -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start">
                            <input type="checkbox" name="privacy_accettata" id="privacy_accettata" required
                                   class="mt-1 h-5 w-5 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded">
                            <label for="privacy_accettata" class="ml-3 text-sm text-gray-700">
                                <span class="font-semibold">Ho letto e accetto la
                                    <a href="{{ route('privacy-policy') }}" target="_blank" class="text-fucsia-magia hover:underline">
                                        Privacy Policy
                                    </a>
                                </span>
                                <span class="text-red-600 font-bold ml-1">*</span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Obbligatorio per l'iscrizione. Include informazioni su come trattiamo i tuoi dati personali.
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- Termini e Condizioni - Obbligatorio -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start">
                            <input type="checkbox" name="termini_accettati" id="termini_accettati" required
                                   class="mt-1 h-5 w-5 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded">
                            <label for="termini_accettati" class="ml-3 text-sm text-gray-700">
                                <span class="font-semibold">Ho letto e accetto i
                                    <a href="{{ route('termini-condizioni') }}" target="_blank" class="text-fucsia-magia hover:underline">
                                        Termini e Condizioni
                                    </a>
                                </span>
                                <span class="text-red-600 font-bold ml-1">*</span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Obbligatorio. Regola il rapporto contrattuale tra te e MA.GIA DONNA.
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- Consenso Dati Sensibili (Salute) - Obbligatorio -->
                    <div class="border border-pink-200 rounded-lg p-4 bg-pink-50">
                        <div class="flex items-start">
                            <input type="checkbox" name="consenso_dati_sensibili" id="consenso_dati_sensibili" required
                                   class="mt-1 h-5 w-5 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded">
                            <label for="consenso_dati_sensibili" class="ml-3 text-sm text-gray-700">
                                <span class="font-semibold">Autorizzo il trattamento dei miei dati sensibili relativi alla salute</span>
                                <span class="text-red-600 font-bold ml-1">*</span>
                                <p class="text-xs text-gray-600 mt-1">
                                    Obbligatorio per la creazione del programma personalizzato. Include peso, altezza,
                                    misure corporee, dati impedenziometrici, note mediche, allergie e obiettivi di benessere.
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- Marketing - Facoltativo -->
                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                        <div class="flex items-start">
                            <input type="checkbox" name="marketing_accettato" id="marketing_accettato"
                                   class="mt-1 h-5 w-5 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded">
                            <label for="marketing_accettato" class="ml-3 text-sm text-gray-700">
                                <span class="font-semibold">Acconsento a ricevere comunicazioni commerciali e newsletter</span>
                                <span class="text-green-600 text-xs ml-2">(Facoltativo)</span>
                                <p class="text-xs text-gray-600 mt-1">
                                    Potrai ricevere informazioni su nuovi programmi, eventi, promozioni speciali e contenuti esclusivi.
                                    Potrai modificare questa preferenza in qualsiasi momento dalla tua area riservata.
                                </p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Info Post-Registrazione -->
                <div class="mt-8 bg-gradient-to-r from-fucsia-magia to-viola-magia bg-opacity-10 border-l-4 border-fucsia-magia p-4 rounded-r-lg">
                    <p class="text-sm text-gray-800 font-semibold mb-2">
                        <i class="fas fa-envelope text-fucsia-magia mr-2"></i>
                        Cosa succede dopo la registrazione?
                    </p>
                    <ul class="text-sm text-gray-700 space-y-1 ml-6 list-disc">
                        <li>Riceverai un'email di benvenuto con le credenziali di accesso</li>
                        <li>Potrai completare il tuo profilo con obiettivi e preferenze</li>
                        <li>Ti invieremo il programma personalizzato Balla & Snella</li>
                        <li>Potrai prenotare la tua giornata di prova gratuita</li>
                    </ul>
                </div>

                <!-- Note GDPR -->
                <div class="mt-4 text-xs text-gray-500 italic bg-gray-100 p-3 rounded">
                    <i class="fas fa-lock mr-1"></i>
                    I tuoi dati sono protetti secondo il Regolamento UE 2016/679 (GDPR).
                    Potrai esercitare i tuoi diritti (accesso, rettifica, cancellazione) in qualsiasi momento
                    contattando <a href="mailto:privacy@donnamagia.it" class="text-fucsia-magia hover:underline">privacy@donnamagia.it</a>
                </div>

                <!-- Pulsanti Navigazione -->
                <div class="mt-8 flex justify-between">
                    <button type="button" @click="sezione = 3"
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Indietro
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg font-bold hover:shadow-xl transition transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i> Completa Registrazione
                    </button>
                </div>
            </div>

        </form>

        <!-- Link Login -->
        <div class="mt-6 text-center">
            <p class="text-white">
                Hai già un account? 
                <a href="{{ route('cliente.login') }}" class="font-bold hover:underline">
                    Accedi qui <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </p>
        </div>

    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
