<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
/**
 * Controller: Gestione Clienti
 * DEBUG: Mostra errori dettagliati
 */
class ClientiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Cliente::orderBy('created_at', 'desc');

            if ($request->filled('cerca')) {
                $cerca = $request->cerca;
                $query->where(function($q) use ($cerca) {
                    $q->where('nome', 'like', "%{$cerca}%")
                      ->orWhere('cognome', 'like', "%{$cerca}%")
                      ->orWhere('email', 'like', "%{$cerca}%")
                      ->orWhere('telefono_mobile', 'like', "%{$cerca}%")
                      ->orWhere('codice_fiscale', 'like', "%{$cerca}%");
                });
            }

            if ($request->filled('stato')) {
                $query->where('stato_cliente', $request->stato);
            }

            if ($request->filled('programma')) {
                $query->where('programma_attuale', $request->programma);
            }

            $clienti = $query->paginate(20)->withQueryString();

            $statistiche = [
                'totale' => Cliente::count(),
                'attive' => Cliente::where('stato_cliente', 'attivo')->count(),
                'sospese' => Cliente::where('stato_cliente', 'sospeso')->count(),
                'inattive' => Cliente::where('stato_cliente', 'inattivo')->count(),
            ];

            $programmi = Cliente::whereNotNull('programma_attuale')
                ->distinct()
                ->pluck('programma_attuale');

            return view('admin.clienti.index', compact('clienti', 'statistiche', 'programmi'));

        } catch (\Exception $e) {
            return view('admin.clienti.index', [
                'clienti' => collect()->paginate(20),
                'statistiche' => ['totale' => 0, 'attive' => 0, 'sospese' => 0, 'inattive' => 0],
                'programmi' => collect([])
            ]);
        }
    }

    public function show($id)
    {
        try {
            $cliente = Cliente::with(['invitatoDa', 'amicheInvitate'])->findOrFail($id);
            return view('admin.clienti.show', compact('cliente'));
        } catch (\Exception $e) {
            return redirect()->route('admin.clienti.index')->with('error', 'Cliente non trovata.');
        }
    }

    public function create()
    {
        return view('admin.clienti.create');
    }

    public function store(Request $request)
    {
        $dati_validati = $request->validate([
            'email' => ['required', 'email', 'unique:utenti,email', 'unique:clienti,email'],
            'password' => ['required', 'min:8'],
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['required', 'string', 'size:16', 'unique:clienti,codice_fiscale'],
            'telefono_mobile' => ['required', 'string', 'max:20'],
            'data_nascita' => ['required', 'date', 'before:today'],
            'indirizzo_via' => ['nullable', 'string', 'max:200'],
            'indirizzo_citta' => ['nullable', 'string', 'max:100'],
            'indirizzo_provincia' => ['nullable', 'string', 'max:2'],
            'indirizzo_cap' => ['nullable', 'string', 'max:5'],
            'programma_attuale' => ['nullable', 'string', 'max:100'],
            'stato_cliente' => ['required', 'in:attivo,sospeso,inattivo'],
        ]);

        DB::beginTransaction();
        
        try {
            $utente = Utente::create([
                'email' => $dati_validati['email'],
                'password' => Hash::make($dati_validati['password']),
                'nome' => $dati_validati['nome'],
                'cognome' => $dati_validati['cognome'],
                'telefono' => $dati_validati['telefono_mobile'],
                'tipo_utente' => 'cliente',
                'attivo' => $dati_validati['stato_cliente'] === 'attivo',
            ]);

            $cliente = Cliente::create([
                'utente_id' => $utente->id,
                'nome' => $dati_validati['nome'],
                'cognome' => $dati_validati['cognome'],
                'codice_fiscale' => strtoupper($dati_validati['codice_fiscale']),
                'email' => $dati_validati['email'],
                'telefono_mobile' => $dati_validati['telefono_mobile'],
                'data_nascita' => $dati_validati['data_nascita'],
                'indirizzo_via' => $dati_validati['indirizzo_via'] ?? null,
                'indirizzo_citta' => $dati_validati['indirizzo_citta'] ?? null,
                'indirizzo_provincia' => !empty($dati_validati['indirizzo_provincia']) ? strtoupper($dati_validati['indirizzo_provincia']) : null,
                'indirizzo_cap' => $dati_validati['indirizzo_cap'] ?? null,
                'programma_attuale' => $dati_validati['programma_attuale'] ?? null,
                'stato_cliente' => $dati_validati['stato_cliente'],
            ]);

            DB::commit();

            return redirect()->route('admin.clienti.index')
                ->with('success', "Cliente {$cliente->nome} {$cliente->cognome} creata con successo!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Errore durante la creazione del cliente.');
        }
    }

    public function edit($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return view('admin.clienti.edit', compact('cliente'));
        } catch (\Exception $e) {
            return redirect()->route('admin.clienti.index')->with('error', 'Cliente non trovata.');
        }
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $regole = [
            'email' => ['required', 'email', 'unique:clienti,email,' . $id],
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['required', 'string', 'size:16', 'unique:clienti,codice_fiscale,' . $id],
            'telefono_mobile' => ['required', 'string', 'max:20'],
            'data_nascita' => ['required', 'date', 'before:today'],
            'indirizzo_via' => ['nullable', 'string', 'max:200'],
            'indirizzo_citta' => ['nullable', 'string', 'max:100'],
            'indirizzo_provincia' => ['nullable', 'string', 'max:2'],
            'indirizzo_cap' => ['nullable', 'string', 'max:5'],
            'programma_attuale' => ['nullable', 'string', 'max:100'],
            'stato_cliente' => ['required', 'in:attivo,sospeso,inattivo'],
            'note_interne' => ['nullable', 'string'],
        ];

        if ($request->filled('password')) {
            $regole['password'] = ['required', 'min:8', 'confirmed'];
        }

        $dati_validati = $request->validate($regole);

        DB::beginTransaction();
        
        try {
            // ⭐ DEBUG: Log dei dati validati
            \Log::info('UPDATE Cliente ID: ' . $id);
            \Log::info('Dati validati:', $dati_validati);

            if ($cliente->utente_id) {
                $utente = Utente::find($cliente->utente_id);
                if ($utente) {
                    $dati_utente = [
                        'email' => $dati_validati['email'],
                        'nome' => $dati_validati['nome'],
                        'cognome' => $dati_validati['cognome'],
                        'telefono' => $dati_validati['telefono_mobile'],
                        'attivo' => $dati_validati['stato_cliente'] === 'attivo',
                    ];

                    if ($request->filled('password')) {
                        $dati_utente['password'] = Hash::make($dati_validati['password']);
                    }

                    \Log::info('Update utente:', $dati_utente);
                    $utente->update($dati_utente);
                    \Log::info('Utente aggiornato OK');
                }
            }

            // Prepara dati cliente
            $dati_cliente = [
                'nome' => $dati_validati['nome'],
                'cognome' => $dati_validati['cognome'],
                'codice_fiscale' => strtoupper($dati_validati['codice_fiscale']),
                'email' => $dati_validati['email'],
                'telefono_mobile' => $dati_validati['telefono_mobile'],
                'data_nascita' => $dati_validati['data_nascita'],
                'indirizzo_via' => $dati_validati['indirizzo_via'] ?? null,
                'indirizzo_citta' => $dati_validati['indirizzo_citta'] ?? null,
                'indirizzo_provincia' => !empty($dati_validati['indirizzo_provincia']) ? strtoupper($dati_validati['indirizzo_provincia']) : null,
                'indirizzo_cap' => $dati_validati['indirizzo_cap'] ?? null,
                'programma_attuale' => $dati_validati['programma_attuale'],
                'stato_cliente' => $dati_validati['stato_cliente'],
                'note_interne' => $dati_validati['note_interne'] ?? null,
            ];

            \Log::info('Update cliente:', $dati_cliente);
            $cliente->update($dati_cliente);
            \Log::info('Cliente aggiornato OK');

            DB::commit();
            \Log::info('Commit OK');

            $messaggio = 'Cliente aggiornata con successo!';
            if ($request->filled('password')) {
                $messaggio = 'Cliente aggiornata e password modificata con successo!';
            }

            return redirect()->route('admin.clienti.index')
                ->with('success', $messaggio);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // ⭐ MOSTRA L'ERRORE DETTAGLIATO
            \Log::error('ERRORE UPDATE Cliente:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mostra errore dettagliato all'utente (SOLO per debug)
            return back()
                ->withInput()
                ->with('error', 'ERRORE: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Riga: ' . $e->getLine());
        }
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $nome = $cliente->nome . ' ' . $cliente->cognome;

            $cliente->delete();
            
            if ($cliente->utente_id) {
                $utente = Utente::find($cliente->utente_id);
                if ($utente) {
                    $utente->delete();
                }
            }

            return redirect()->route('admin.clienti.index')
                ->with('success', "Cliente {$nome} eliminata con successo.");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.clienti.index')->with('error', 'Errore durante l\'eliminazione del cliente.');
        }
    }
}