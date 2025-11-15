<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\NotificaNuovaIscrizioneProva;
use Illuminate\Support\Facades\Mail;

/**
 * Landing Page Giornata di Prova
 *
 * Gestisce la landing page per la giornata di prova gratuita.
 * Form integrato che salva automaticamente nel database.
 */
class LandingPageController extends Controller
{
    /**
     * Mostra landing page
     */
    public function index()
    {
        return view('landing.giornata-prova');
    }

    /**
     * Registra cliente per giornata di prova
     */
    public function registraProva(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'required|string|max:20',
            'sede_preferita' => 'nullable|exists:sedi,id',
            'note' => 'nullable|string|max:1000',
            'privacy_accepted' => 'required|accepted',
        ], [
            'email.unique' => 'Questa email è già registrata. Accedi al tuo account.',
            'privacy_accepted.accepted' => 'Devi accettare la privacy policy per continuare.',
        ]);

        try {
            // Crea utente con stato "Cliente di Prova"
            $passwordTemporanea = Str::random(12);

            $user = DB::table('users')->insertGetId([
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'password' => Hash::make($passwordTemporanea),
                'tipo_utente' => 'cliente',
                'stato' => 'cliente_prova', // ⭐ Stato speciale
                'sede_preferita_id' => $validated['sede_preferita'] ?? null,
                'note_iscrizione' => $validated['note'] ?? null,
                'privacy_accepted' => true,
                'privacy_accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Notifica automatica all'amministratore
            $this->notificaAdmin($user, $validated);

            // Redirect con successo
            return redirect()->route('landing.giornata-prova.grazie')
                ->with('success', 'Registrazione completata! Verrai contattata a breve per confermare la tua giornata di prova.');

        } catch (\Exception $e) {
            \Log::error('Errore registrazione giornata prova: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Si è verificato un errore. Riprova o contattaci direttamente.');
        }
    }

    /**
     * Pagina thank you
     */
    public function grazie()
    {
        return view('landing.grazie');
    }

    /**
     * Notifica admin per nuova iscrizione
     */
    private function notificaAdmin($userId, $dati)
    {
        try {
            // Invia email a tutti gli admin
            $admins = DB::table('users')
                ->where('tipo_utente', 'admin')
                ->orWhere('tipo_utente', 'super_admin')
                ->get();

            foreach ($admins as $admin) {
                // Mail::to($admin->email)->send(new NotificaNuovaIscrizioneProva($dati));
                // Commenta per ora, da implementare dopo configurazione SMTP
            }

            // Log per debug
            \Log::info('Nuova iscrizione giornata prova', [
                'user_id' => $userId,
                'nome' => $dati['nome'],
                'email' => $dati['email'],
            ]);

        } catch (\Exception $e) {
            \Log::error('Errore notifica admin: ' . $e->getMessage());
        }
    }
}
