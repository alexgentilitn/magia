<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controller: Autenticazione
 * Funzione: Gestisce login separati per Admin e Clienti
 */
class AuthController extends Controller
{
    /**
     * Mostra form login ADMIN
     */
    public function mostraLoginAdmin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->tipo_utente === 'amministratore') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->tipo_utente === 'professionista') {
                return redirect()->route('professionista.dashboard');
            }
        }

        return view('admin.auth.login');
    }

    /**
     * Processa login ADMIN
     */
    public function loginAdmin(Request $request)
    {
        // Validazione
        $credenziali = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'password.required' => 'La password è obbligatoria',
        ]);

        // Verifica che l'utente esista
        $utente = Utente::where('email', $credenziali['email'])->first();

        if (!$utente) {
            throw ValidationException::withMessages([
                'email' => 'Le credenziali inserite non sono corrette.',
            ]);
        }

        // Verifica che sia admin o professionista
        if (!in_array($utente->tipo_utente, ['amministratore', 'professionista'])) {
            throw ValidationException::withMessages([
                'email' => 'Non hai i permessi per accedere a questa area.',
            ]);
        }

        // Verifica che sia attivo
        if (!$utente->attivo) {
            throw ValidationException::withMessages([
                'email' => 'Il tuo account è stato disattivato.',
            ]);
        }

        // Tentativo di login
        $remember = $request->boolean('remember');

        if (Auth::attempt($credenziali, $remember)) {
            $request->session()->regenerate();

            // Aggiorna ultimo accesso
            $utente->ultimo_accesso = now();
            $utente->ultimo_ip = $request->ip();
            $utente->save();

            // Redirect in base al tipo utente
            if ($utente->tipo_utente === 'professionista') {
                return redirect()->route('professionista.dashboard')
                    ->with('success', "Benvenuto {$utente->nome}!");
            } else {
                return redirect()->route('admin.dashboard')
                    ->with('success', "Benvenuto {$utente->nome}!");
            }
        }

        throw ValidationException::withMessages([
            'email' => 'Le credenziali inserite non sono corrette.',
        ]);
    }

    /**
     * Mostra form login CLIENTE
     */
    public function mostraLoginCliente()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->tipo_utente === 'cliente') {
                return redirect()->route('cliente.dashboard');
            }
        }
        
        return view('cliente.auth.login');
    }

    /**
     * Processa login CLIENTE
     */
    public function loginCliente(Request $request)
    {
        // Validazione
        $credenziali = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'password.required' => 'La password è obbligatoria',
        ]);

        // Verifica che l'utente esista
        $utente = Utente::where('email', $credenziali['email'])->first();

        if (!$utente) {
            throw ValidationException::withMessages([
                'email' => 'Le credenziali inserite non sono corrette.',
            ]);
        }

        // Verifica che sia cliente
        if ($utente->tipo_utente !== 'cliente') {
            throw ValidationException::withMessages([
                'email' => 'Usa il login amministratore per accedere.',
            ]);
        }

        // Verifica che sia attivo
        if (!$utente->attivo) {
            throw ValidationException::withMessages([
                'email' => 'Il tuo account è stato disattivato.',
            ]);
        }

        // Tentativo di login
        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credenziali, $remember)) {
            $request->session()->regenerate();
            
            // Aggiorna ultimo accesso
            $utente->ultimo_accesso = now();
            $utente->ultimo_ip = $request->ip();
            $utente->save();
            
            return redirect()->route('cliente.dashboard')
                ->with('success', "Bentornata {$utente->nome}!");
        }

        throw ValidationException::withMessages([
            'email' => 'Le credenziali inserite non sono corrette.',
        ]);
    }

    /**
     * Logout globale
     */
    public function effettuaLogout(Request $request)
    {
        $nome_utente = Auth::user()->nome;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', "Arrivederci {$nome_utente}!");
    }

    // ============================================
    // RECUPERO PASSWORD ADMIN
    // ============================================

    /**
     * Mostra form richiesta reset password
     */
    public function mostraFormResetPassword()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Invia email con link reset password
     */
    public function inviaLinkResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
        ]);

        // Verifica che l'utente esista e sia admin/professionista
        // Confronto case-insensitive per l'email
        $utente = Utente::whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->whereIn('tipo_utente', ['amministratore', 'professionista'])
            ->first();

        if (!$utente) {
            // Per motivi di sicurezza, mostriamo sempre lo stesso messaggio
            return back()->with('success', 'Se l\'email esiste nel sistema, riceverai un link per reimpostare la password.');
        }

        // Genera token univoco
        $token = \Str::random(64);

        // Salva o aggiorna token nel database (usa email dal database per mantenere il case originale)
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $utente->email],
            [
                'email' => $utente->email,
                'token' => \Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Invia email
        try {
            // Applica configurazioni SMTP dal database
            if (class_exists(\App\Models\Impostazione::class)) {
                \App\Models\Impostazione::applySmtpConfig();
            }

            \Mail::to($utente->email)->send(new \App\Mail\ResetPasswordMail($utente, $token));
        } catch (\Exception $e) {
            \Log::error('Errore invio email reset password: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Errore nell\'invio dell\'email. Riprova più tardi.']);
        }

        return back()->with('success', 'Se l\'email esiste nel sistema, riceverai un link per reimpostare la password.');
    }

    /**
     * Mostra form per impostare nuova password
     */
    public function mostraFormNuovaPassword($token)
    {
        return view('admin.auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'La password deve essere di almeno :min caratteri',
            'password.confirmed' => 'Le password non corrispondono',
        ]);

        // Verifica token (confronto case-insensitive per email)
        $resetRecord = \DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Token non valido o scaduto.']);
        }

        // Verifica che il token corrisponda
        if (!\Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Token non valido o scaduto.']);
        }

        // Verifica che il token non sia scaduto (24 ore)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            \DB::table('password_reset_tokens')->whereRaw('LOWER(email) = ?', [strtolower($request->email)])->delete();
            return back()->withErrors(['email' => 'Il link è scaduto. Richiedi un nuovo link di reset.']);
        }

        // Trova utente (confronto case-insensitive per email)
        $utente = Utente::whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->whereIn('tipo_utente', ['amministratore', 'professionista'])
            ->first();

        if (!$utente) {
            return back()->withErrors(['email' => 'Utente non trovato.']);
        }

        // Aggiorna password
        $utente->password = \Hash::make($request->password);
        $utente->save();

        // Elimina token usato
        \DB::table('password_reset_tokens')->whereRaw('LOWER(email) = ?', [strtolower($request->email)])->delete();

        // Redirect al login con messaggio di successo
        return redirect()->route('admin.login')
            ->with('success', 'Password reimpostata con successo! Ora puoi accedere con la nuova password.');
    }
}