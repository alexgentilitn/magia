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
 * Export Excel Calendario Lezioni
 *
 * Esporta calendario lezioni in formato Excel
 */
class LezioniExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $dataInizio;
    protected $dataFine;
    protected $filtri;

    public function __construct($dataInizio, $dataFine, $filtri = [])
    {
        $this->dataInizio = $dataInizio;
        $this->dataFine = $dataFine;
        $this->filtri = $filtri;
    }

    /**
     * Recupera dati da esportare
     */
    public function collection()
    {
        $query = Lezione::with(['professionista', 'sede', 'programma'])
            ->whereBetween('data', [$this->dataInizio, $this->dataFine]);

        // Applica filtri opzionali
        if (!empty($this->filtri['professionista_id'])) {
            $query->where('professionista_id', $this->filtri['professionista_id']);
        }

        if (!empty($this->filtri['sede_id'])) {
            $query->where('sede_id', $this->filtri['sede_id']);
        }

        if (!empty($this->filtri['tipologia'])) {
            $query->where('tipologia', $this->filtri['tipologia']);
        }

        if (!empty($this->filtri['stato'])) {
            $query->where('stato', $this->filtri['stato']);
        }

        return $query->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();
    }

    /**
     * Definisce intestazioni colonne
     */
    public function headings(): array
    {
        return [
            'ID',
            'Data',
            'Giorno',
            'Ora Inizio',
            'Ora Fine',
            'Durata (min)',
            'Titolo',
            'Programma',
            'Professionista',
            'Sede',
            'Tipologia',
            'Stato',
            'Posti Occupati',
            'Posti Totali',
            'Note',
        ];
    }

    /**
     * Mappa i dati per ogni riga
     */
    public function map($lezione): array
    {
        $data = Carbon::parse($lezione->data);

        return [
            $lezione->id,
            $data->format('d/m/Y'),
            $data->isoFormat('dddd'), // Nome giorno
            Carbon::parse($lezione->ora_inizio)->format('H:i'),
            Carbon::parse($lezione->ora_fine)->format('H:i'),
            $lezione->durata_minuti,
            $lezione->titolo,
            $lezione->programma ? $lezione->programma->nome : '-',
            $lezione->professionista
                ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome
                : 'Non assegnato',
            $lezione->sede ? $lezione->sede->nome : 'Online',
            ucfirst($lezione->tipologia),
            ucfirst($lezione->stato),
            $lezione->posti_occupati,
            $lezione->posti_totali,
            $lezione->note_pubbliche ?? '',
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
                    'startColor' => ['rgb' => 'E91E8C'] // fucsia-magia
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
        return 'Calendario Lezioni';
    }
}
