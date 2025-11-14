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
            if (in_array($user->tipo_utente, ['amministratore', 'professionista'])) {
                return redirect()->route('admin.dashboard');
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
            
            return redirect()->route('admin.dashboard')
                ->with('success', "Benvenuto {$utente->nome}!");
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
}