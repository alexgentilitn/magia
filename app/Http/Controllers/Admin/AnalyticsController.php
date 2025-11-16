<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $periodo = request('periodo', 30);

        $statistiche = [
            'totale_eventi' => Analytics::recenti($periodo)->count(),
            'utenti_unici' => Analytics::recenti($periodo)->distinct('utente_id')->count('utente_id'),
            'page_views' => Analytics::recenti($periodo)->perTipo('page_view')->count(),
            'mobile_percent' => $this->calcolaPercentualeMobile($periodo),
        ];

        $pagine_piu_visitate = Analytics::recenti($periodo)
            ->perTipo('page_view')
            ->select('pagina', DB::raw('count(*) as visite'))
            ->groupBy('pagina')
            ->orderBy('visite', 'desc')
            ->limit(10)
            ->get();

        $dispositivi = Analytics::recenti($periodo)
            ->select('dispositivo', DB::raw('count(*) as totale'))
            ->groupBy('dispositivo')
            ->get();

        $browser = Analytics::recenti($periodo)
            ->select('browser', DB::raw('count(*) as totale'))
            ->groupBy('browser')
            ->orderBy('totale', 'desc')
            ->get();

        return view('admin.analytics.index', compact('statistiche', 'pagine_piu_visitate', 'dispositivi', 'browser', 'periodo'));
    }

    public function eventi()
    {
        $eventi = Analytics::with(['utente', 'cliente'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(50);

        return view('admin.analytics.eventi', compact('eventi'));
    }

    public function utente($utente_id)
    {
        $eventi = Analytics::where('utente_id', $utente_id)
                          ->orderBy('created_at', 'desc')
                          ->paginate(50);

        $statistiche = [
            'totale_eventi' => Analytics::where('utente_id', $utente_id)->count(),
            'page_views' => Analytics::where('utente_id', $utente_id)->perTipo('page_view')->count(),
            'tempo_medio' => Analytics::where('utente_id', $utente_id)->avg('tempo_permanenza'),
        ];

        return view('admin.analytics.utente', compact('eventi', 'statistiche', 'utente_id'));
    }

    private function calcolaPercentualeMobile($periodo)
    {
        $totale = Analytics::recenti($periodo)->count();
        if ($totale == 0) return 0;

        $mobile = Analytics::recenti($periodo)->mobile()->count();
        return round(($mobile / $totale) * 100, 1);
    }

    public function export(Request $request)
    {
        $periodo = $request->get('periodo', 30);

        $eventi = Analytics::recenti($periodo)
                          ->orderBy('created_at', 'desc')
                          ->get();

        $csv = "Data,Tipo Evento,Pagina,Azione,Dispositivo,Browser,OS,IP\n";

        foreach ($eventi as $evento) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $evento->created_at,
                $evento->tipo_evento,
                $evento->pagina,
                $evento->azione,
                $evento->dispositivo,
                $evento->browser,
                $evento->os,
                $evento->ip_address
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics_' . now()->format('Y-m-d') . '.csv"');
    }
}
