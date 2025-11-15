<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;

/**
 * Controller: Location (Sedi Pubbliche)
 * Gestisce la visualizzazione pubblica delle sedi MA.GIA DONNA
 */
class LocationController extends Controller
{
    /**
     * Mostra la mappa interattiva con tutte le sedi
     */
    public function index()
    {
        // Recupera solo le sedi attive e visibili al pubblico
        $sedi = Sede::attive()
            ->visibili()
            ->orderBy('ordine')
            ->orderBy('nome')
            ->get();

        // Prepara i dati per Google Maps
        $locations = $sedi->map(function ($sede) {
            return [
                'id' => $sede->id,
                'nome' => $sede->nome,
                'indirizzo' => $sede->indirizzo_completo,
                'lat' => (float) $sede->latitudine,
                'lng' => (float) $sede->longitudine,
                'telefono' => $sede->telefono,
                'email' => $sede->email,
                'descrizione' => $sede->descrizione,
                'orari' => $sede->orari_apertura,
                'slug' => $sede->slug,
                'immagine' => $sede->immagine_copertina,
                'tipologie_corsi' => $sede->tipologie_corsi ?? [],
            ];
        });

        // Calcola il centro della mappa (media delle coordinate)
        if ($sedi->count() > 0) {
            $centerLat = $sedi->avg('latitudine');
            $centerLng = $sedi->avg('longitudine');
        } else {
            // Default: Centro Italia (Roma)
            $centerLat = 41.9028;
            $centerLng = 12.4964;
        }

        return view('locations.index', compact('sedi', 'locations', 'centerLat', 'centerLng'));
    }

    /**
     * Mostra il dettaglio di una singola sede
     */
    public function show($slug)
    {
        $sede = Sede::where('slug', $slug)
            ->attive()
            ->visibili()
            ->firstOrFail();

        return view('locations.show', compact('sede'));
    }

    /**
     * API per ottenere le sedi in formato JSON (per chiamate AJAX)
     */
    public function api()
    {
        $sedi = Sede::attive()
            ->visibili()
            ->orderBy('ordine')
            ->orderBy('nome')
            ->get()
            ->map(function ($sede) {
                return [
                    'id' => $sede->id,
                    'nome' => $sede->nome,
                    'indirizzo' => $sede->indirizzo_completo,
                    'lat' => (float) $sede->latitudine,
                    'lng' => (float) $sede->longitudine,
                    'telefono' => $sede->telefono,
                    'email' => $sede->email,
                    'descrizione' => $sede->descrizione,
                    'slug' => $sede->slug,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $sedi->count(),
            'locations' => $sedi,
        ]);
    }
}
