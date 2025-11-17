<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateEmail;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        $templates = TemplateEmail::orderBy('tipo', 'asc')->get();

        return view('admin.template-email.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.template-email.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|max:50|unique:template_email,tipo',
            'nome' => 'required|string|max:255',
            'oggetto' => 'required|string|max:255',
            'corpo_html' => 'required',
            'corpo_text' => 'nullable',
            'mittente_nome' => 'nullable|string|max:255',
            'mittente_email' => 'nullable|email|max:255',
            'variabili_disponibili' => 'nullable|json',
            'note' => 'nullable',
            'attivo' => 'boolean',
            'invia_automaticamente' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Converti variabili da stringa JSON se presente
        $variabili = null;
        if ($request->variabili_disponibili) {
            $variabili = json_decode($request->variabili_disponibili, true);
        }

        $template = TemplateEmail::create([
            'tipo' => $request->tipo,
            'nome' => $request->nome,
            'oggetto' => $request->oggetto,
            'corpo_html' => $request->corpo_html,
            'corpo_text' => $request->corpo_text,
            'mittente_nome' => $request->mittente_nome,
            'mittente_email' => $request->mittente_email,
            'variabili_disponibili' => $variabili,
            'note' => $request->note,
            'attivo' => $request->has('attivo'),
            'invia_automaticamente' => $request->has('invia_automaticamente'),
        ]);

        return redirect()->route('admin.template-email.index')
                         ->with('success', 'Template email creato con successo!');
    }

    public function show($id)
    {
        $template = TemplateEmail::findOrFail($id);

        return view('admin.template-email.show', compact('template'));
    }

    public function edit($id)
    {
        $template = TemplateEmail::findOrFail($id);

        return view('admin.template-email.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = TemplateEmail::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|max:50|unique:template_email,tipo,' . $id,
            'nome' => 'required|string|max:255',
            'oggetto' => 'required|string|max:255',
            'corpo_html' => 'required',
            'corpo_text' => 'nullable',
            'mittente_nome' => 'nullable|string|max:255',
            'mittente_email' => 'nullable|email|max:255',
            'variabili_disponibili' => 'nullable|json',
            'note' => 'nullable',
            'attivo' => 'boolean',
            'invia_automaticamente' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Converti variabili da stringa JSON se presente
        $variabili = null;
        if ($request->variabili_disponibili) {
            $variabili = json_decode($request->variabili_disponibili, true);
        }

        $template->update([
            'tipo' => $request->tipo,
            'nome' => $request->nome,
            'oggetto' => $request->oggetto,
            'corpo_html' => $request->corpo_html,
            'corpo_text' => $request->corpo_text,
            'mittente_nome' => $request->mittente_nome,
            'mittente_email' => $request->mittente_email,
            'variabili_disponibili' => $variabili,
            'note' => $request->note,
            'attivo' => $request->has('attivo'),
            'invia_automaticamente' => $request->has('invia_automaticamente'),
        ]);

        return redirect()->route('admin.template-email.index')
                         ->with('success', 'Template email aggiornato con successo!');
    }

    public function destroy($id)
    {
        $template = TemplateEmail::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.template-email.index')
                         ->with('success', 'Template email eliminato con successo!');
    }

    public function preview(Request $request, $id)
    {
        $template = TemplateEmail::findOrFail($id);

        // Variabili di esempio per preview
        $variabili = [
            'nome_cliente' => 'Mario Rossi',
            'cognome_cliente' => 'Rossi',
            'email_cliente' => 'mario.rossi@example.com',
            'nome_programma' => 'Programma Dimagrimento Base',
            'data_lezione' => '15/06/2024',
            'ora_lezione' => '10:00',
            'sede' => 'Sede Centrale',
            'importo' => '€ 150,00',
            'data_scadenza' => '30/06/2024',
            'link_prenotazione' => 'https://www.agstudio.digital/magia/public/cliente/calendario',
            'link_area_personale' => 'https://www.agstudio.digital/magia/public/cliente/dashboard',
        ];

        $renderizzato = $template->renderizza($variabili);

        return response()->json([
            'oggetto' => $renderizzato['oggetto'],
            'corpo_html' => $renderizzato['corpo_html'],
            'corpo_text' => $renderizzato['corpo_text'],
        ]);
    }

    public function inviaTest(Request $request, $id)
    {
        $template = TemplateEmail::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email_test' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Variabili di esempio per test
        $variabili = [
            'nome_cliente' => 'Test',
            'cognome_cliente' => 'Utente',
            'email_cliente' => $request->email_test,
            'nome_programma' => 'Programma di Test',
            'data_lezione' => now()->format('d/m/Y'),
            'ora_lezione' => '10:00',
            'sede' => 'Sede Test',
            'importo' => '€ 100,00',
            'data_scadenza' => now()->addDays(30)->format('d/m/Y'),
            'link_prenotazione' => route('cliente.calendario'),
            'link_area_personale' => route('cliente.dashboard'),
        ];

        try {
            $this->emailService->inviaConTemplate(
                $template->id,
                $request->email_test,
                $variabili
            );

            return back()->with('success', 'Email di test inviata con successo a ' . $request->email_test);
        } catch (\Exception $e) {
            return back()->with('error', 'Errore durante l\'invio dell\'email: ' . $e->getMessage());
        }
    }

    public function toggleAttivo($id)
    {
        $template = TemplateEmail::findOrFail($id);
        $template->attivo = !$template->attivo;
        $template->save();

        $stato = $template->attivo ? 'attivato' : 'disattivato';

        return redirect()->route('admin.template-email.index')
                         ->with('success', 'Template ' . $stato . ' con successo!');
    }
}
