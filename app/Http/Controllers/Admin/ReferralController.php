<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::with(['cliente', 'clienteInvitato'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

        $statistiche = [
            'totale_inviti' => Referral::count(),
            'in_attesa' => Referral::inAttesa()->count(),
            'iscritti' => Referral::where('stato', 'iscritto')->count(),
            'attivi' => Referral::attivi()->count(),
            'sconti_da_applicare' => Referral::scontoNonApplicato()->count(),
        ];

        return view('admin.referral.index', compact('referrals', 'statistiche'));
    }

    public function show($id)
    {
        $referral = Referral::with(['cliente', 'clienteInvitato'])->findOrFail($id);

        return view('admin.referral.show', compact('referral'));
    }

    public function cambiaStato(Request $request, $id)
    {
        $referral = Referral::findOrFail($id);

        $validated = $request->validate([
            'stato' => 'required|in:in_attesa,iscritto,attivo,sconto_applicato,annullato',
        ]);

        $referral->update($validated);

        return redirect()->back()->with('success', 'Stato referral aggiornato');
    }

    public function applicaSconto($id)
    {
        $referral = Referral::findOrFail($id);

        $referral->update([
            'sconto_applicato' => true,
            'stato' => 'sconto_applicato',
        ]);

        return redirect()->back()->with('success', 'Sconto applicato al referral');
    }

    public function configurazione()
    {
        return view('admin.referral.configurazione');
    }

    public function salvaConfigurazione(Request $request)
    {
        $validated = $request->validate([
            'sconto_default_tipo' => 'required|string',
            'sconto_default_valore' => 'required|numeric',
        ]);

        // Salva in impostazioni sistema
        \App\Models\Impostazione::set('referral_sconto_tipo', $validated['sconto_default_tipo']);
        \App\Models\Impostazione::set('referral_sconto_valore', $validated['sconto_default_valore']);

        return redirect()->back()->with('success', 'Configurazione referral salvata');
    }

    public function report()
    {
        $top_referrer = Cliente::withCount('amicheInvitate')
                              ->having('amiche_invitate_count', '>', 0)
                              ->orderBy('amiche_invitate_count', 'desc')
                              ->take(10)
                              ->get();

        return view('admin.referral.report', compact('top_referrer'));
    }
}
