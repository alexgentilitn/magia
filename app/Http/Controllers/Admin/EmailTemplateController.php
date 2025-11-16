<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        // Filtro per tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro solo attivi
        if ($request->boolean('solo_attivi')) {
            $query->where('attivo', true);
        }

        // Ricerca per nome/codice
        if ($request->filled('ricerca')) {
            $ricerca = $request->ricerca;
            $query->where(function($q) use ($ricerca) {
                $q->where('nome', 'like', "%{$ricerca}%")
                  ->orWhere('codice', 'like', "%{$ricerca}%")
                  ->orWhere('oggetto', 'like', "%{$ricerca}%");
            });
        }

        // Ordinamento
        $query->orderBy('tipo', 'asc')
              ->orderBy('nome', 'asc');

        $templates = $query->paginate(15)->withQueryString();

        // Statistiche
        $statistiche = [
            'totali' => EmailTemplate::count(),
            'attivi' => EmailTemplate::where('attivo', true)->count(),
            'disattivi' => EmailTemplate::where('attivo', false)->count(),
        ];

        // Tipi disponibili per i filtri
        $tipi = EmailTemplate::distinct()->pluck('tipo')->filter();

        return view('admin.email-templates.index', compact('templates', 'statistiche', 'tipi'));
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create()
    {
        // Tipi di template disponibili
        $tipiDisponibili = [
            'sistema' => 'Sistema',
            'marketing' => 'Marketing',
            'notifica' => 'Notifica',
            'promemoria' => 'Promemoria',
            'referral' => 'Referral',
            'altro' => 'Altro'
        ];

        // Variabili comuni disponibili
        $variabiliComuni = [
            'nome_cliente' => 'Nome del cliente',
            'cognome_cliente' => 'Cognome del cliente',
            'email_cliente' => 'Email del cliente',
            'data_corrente' => 'Data corrente',
            'nome_azienda' => 'Nome azienda (MA.GIA)',
            'email_azienda' => 'Email azienda',
            'telefono_azienda' => 'Telefono azienda',
            'indirizzo_azienda' => 'Indirizzo azienda',
        ];

        return view('admin.email-templates.create', compact('tipiDisponibili', 'variabiliComuni'));
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codice' => 'required|string|max:100|unique:email_templates,codice',
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'oggetto' => 'required|string|max:255',
            'corpo_html' => 'required|string',
            'corpo_testo' => 'nullable|string',
            'variabili_disponibili' => 'nullable|array',
            'attivo' => 'boolean'
        ], [
            'codice.required' => 'Il codice è obbligatorio',
            'codice.unique' => 'Questo codice è già in uso',
            'nome.required' => 'Il nome è obbligatorio',
            'tipo.required' => 'Il tipo è obbligatorio',
            'oggetto.required' => 'L\'oggetto è obbligatorio',
            'corpo_html.required' => 'Il corpo HTML è obbligatorio',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Genera codice slug-friendly se non fornito
        $codice = $request->codice;
        if (!preg_match('/^[a-z0-9_-]+$/', $codice)) {
            $codice = Str::slug($codice, '_');
        }

        // Se corpo_testo non fornito, genera da HTML
        $corpoTesto = $request->corpo_testo;
        if (empty($corpoTesto)) {
            $corpoTesto = strip_tags($request->corpo_html);
        }

        $template = EmailTemplate::create([
            'codice' => $codice,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'oggetto' => $request->oggetto,
            'corpo_html' => $request->corpo_html,
            'corpo_testo' => $corpoTesto,
            'variabili_disponibili' => $request->variabili_disponibili ?? [],
            'attivo' => $request->boolean('attivo', true)
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email creato con successo!');
    }

    /**
     * Display the specified email template.
     */
    public function show($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.email-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        // Tipi di template disponibili
        $tipiDisponibili = [
            'sistema' => 'Sistema',
            'marketing' => 'Marketing',
            'notifica' => 'Notifica',
            'promemoria' => 'Promemoria',
            'referral' => 'Referral',
            'altro' => 'Altro'
        ];

        // Variabili comuni disponibili
        $variabiliComuni = [
            'nome_cliente' => 'Nome del cliente',
            'cognome_cliente' => 'Cognome del cliente',
            'email_cliente' => 'Email del cliente',
            'data_corrente' => 'Data corrente',
            'nome_azienda' => 'Nome azienda (MA.GIA)',
            'email_azienda' => 'Email azienda',
            'telefono_azienda' => 'Telefono azienda',
            'indirizzo_azienda' => 'Indirizzo azienda',
        ];

        return view('admin.email-templates.edit', compact('template', 'tipiDisponibili', 'variabiliComuni'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codice' => 'required|string|max:100|unique:email_templates,codice,' . $id,
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'oggetto' => 'required|string|max:255',
            'corpo_html' => 'required|string',
            'corpo_testo' => 'nullable|string',
            'variabili_disponibili' => 'nullable|array',
            'attivo' => 'boolean'
        ], [
            'codice.required' => 'Il codice è obbligatorio',
            'codice.unique' => 'Questo codice è già in uso',
            'nome.required' => 'Il nome è obbligatorio',
            'tipo.required' => 'Il tipo è obbligatorio',
            'oggetto.required' => 'L\'oggetto è obbligatorio',
            'corpo_html.required' => 'Il corpo HTML è obbligatorio',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Genera codice slug-friendly se non fornito
        $codice = $request->codice;
        if (!preg_match('/^[a-z0-9_-]+$/', $codice)) {
            $codice = Str::slug($codice, '_');
        }

        // Se corpo_testo non fornito, genera da HTML
        $corpoTesto = $request->corpo_testo;
        if (empty($corpoTesto)) {
            $corpoTesto = strip_tags($request->corpo_html);
        }

        $template->update([
            'codice' => $codice,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'oggetto' => $request->oggetto,
            'corpo_html' => $request->corpo_html,
            'corpo_testo' => $corpoTesto,
            'variabili_disponibili' => $request->variabili_disponibili ?? [],
            'attivo' => $request->boolean('attivo', $template->attivo)
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email aggiornato con successo!');
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email eliminato con successo!');
    }

    /**
     * Toggle the active status of an email template.
     */
    public function toggleAttivo($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->attivo = !$template->attivo;
        $template->save();

        $stato = $template->attivo ? 'attivato' : 'disattivato';

        return redirect()->back()
            ->with('success', "Template email {$stato} con successo!");
    }

    /**
     * Duplicate an email template.
     */
    public function duplica($id)
    {
        $templateOriginale = EmailTemplate::findOrFail($id);

        // Trova un codice unico
        $nuovoCodice = $templateOriginale->codice . '_copia';
        $counter = 1;
        while (EmailTemplate::where('codice', $nuovoCodice)->exists()) {
            $nuovoCodice = $templateOriginale->codice . '_copia_' . $counter;
            $counter++;
        }

        $nuovoTemplate = EmailTemplate::create([
            'codice' => $nuovoCodice,
            'nome' => $templateOriginale->nome . ' (Copia)',
            'tipo' => $templateOriginale->tipo,
            'oggetto' => $templateOriginale->oggetto,
            'corpo_html' => $templateOriginale->corpo_html,
            'corpo_testo' => $templateOriginale->corpo_testo,
            'variabili_disponibili' => $templateOriginale->variabili_disponibili,
            'attivo' => false // Il duplicato inizia come disattivato
        ]);

        return redirect()->route('admin.email-templates.edit', $nuovoTemplate->id)
            ->with('success', 'Template email duplicato con successo!');
    }

    /**
     * Send a test email using the template.
     */
    public function inviaTest(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email_test' => 'required|email'
        ], [
            'email_test.required' => 'L\'email di destinazione è obbligatoria',
            'email_test.email' => 'Inserisci un indirizzo email valido'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Variabili di test
        $variabiliTest = [
            'nome_cliente' => 'Mario',
            'cognome_cliente' => 'Rossi',
            'email_cliente' => $request->email_test,
            'data_corrente' => now()->format('d/m/Y'),
            'nome_azienda' => 'MA.GIA DONNA',
            'email_azienda' => config('mail.from.address', 'info@magiadonna.it'),
            'telefono_azienda' => '+39 xxx xxx xxxx',
            'indirizzo_azienda' => 'Via esempio, 123',
        ];

        // Processa il template
        $emailProcessata = $template->processa($variabiliTest);

        try {
            Mail::send([], [], function ($message) use ($request, $emailProcessata) {
                $message->to($request->email_test)
                    ->subject('[TEST] ' . $emailProcessata['oggetto'])
                    ->html($emailProcessata['corpo_html']);
            });

            return redirect()->back()
                ->with('success', 'Email di test inviata con successo a ' . $request->email_test);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nell\'invio dell\'email: ' . $e->getMessage());
        }
    }
}
