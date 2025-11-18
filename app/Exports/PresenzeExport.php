<?php

namespace App\Exports;

use App\Models\Lezione;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Excel Report Presenze
 *
 * Esporta report dettagliato delle presenze in formato Excel
 */
class PresenzeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $dataInizio;
    protected $dataFine;

    public function __construct($dataInizio, $dataFine)
    {
        $this->dataInizio = $dataInizio;
        $this->dataFine = $dataFine;
    }

    /**
     * Recupera dati da esportare
     */
    public function collection()
    {
        return Lezione::with(['professionista', 'sede', 'clienti' => function($query) {
                $query->wherePivot('stato', 'presente');
            }])
            ->whereBetween('data', [$this->dataInizio, $this->dataFine])
            ->whereIn('stato', ['completata', 'in_corso'])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();
    }

    /**
     * Definisce intestazioni colonne
     */
    public function headings(): array
    {
        return [
            'Data',
            'Ora Inizio',
            'Ora Fine',
            'Lezione',
            'Professionista',
            'Sede',
            'Tipologia',
            'Stato',
            'Presenti',
            'Posti Totali',
            '% Occupazione',
        ];
    }

    /**
     * Mappa i dati per ogni riga
     */
    public function map($lezione): array
    {
        $presenti = $lezione->clienti->count();
        $occupazione = $lezione->posti_totali > 0
            ? round(($presenti / $lezione->posti_totali) * 100, 1)
            : 0;

        return [
            Carbon::parse($lezione->data)->format('d/m/Y'),
            Carbon::parse($lezione->ora_inizio)->format('H:i'),
            Carbon::parse($lezione->ora_fine)->format('H:i'),
            $lezione->titolo,
            $lezione->professionista
                ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome
                : 'Non assegnato',
            $lezione->sede ? $lezione->sede->nome : 'Online',
            ucfirst($lezione->tipologia),
            ucfirst($lezione->stato),
            $presenti,
            $lezione->posti_totali,
            $occupazione . '%',
        ];
    }

    /**
     * Applica stili al foglio
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Stile intestazione
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7B2869'] // viola-magia
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    /**
     * Titolo foglio Excel
     */
    public function title(): string
    {
        return 'Report Presenze';
    }
}
