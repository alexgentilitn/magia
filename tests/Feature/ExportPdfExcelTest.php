<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utente;
use App\Models\Cliente;
use App\Models\Lezione;
use App\Models\Professionista;
use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test per export PDF e Excel
 *
 * Verifica che le funzionalità di export funzionino correttamente
 * e identifica eventuali problemi con le librerie
 */
class ExportPdfExcelTest extends TestCase
{
    use RefreshDatabase;

    private Utente $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Utente::factory()->amministratore()->create();
    }

    /** @test */
    public function export_excel_presenze_funziona()
    {
        // Crea dati di test
        $sede = Sede::factory()->create();
        $utenteProfessionista = Utente::factory()->professionista()->create();
        $professionista = Professionista::factory()->create(['utente_id' => $utenteProfessionista->id]);

        $lezione = Lezione::factory()->create([
            'sede_id' => $sede->id,
            'professionista_id' => $professionista->id,
        ]);

        $cliente = Cliente::factory()->create();

        // Prenota cliente (presente)
        \DB::table('cliente_lezione')->insert([
            'cliente_id' => $cliente->utente_id, // cliente_lezione.cliente_id → utenti.id
            'lezione_id' => $lezione->id,
            'stato' => 'presente', // Enum: 'presente' non boolean
            'check_in' => now()->subHours(2),
            'check_out' => now()->subHours(1),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tenta export Excel presenze
        $response = $this->actingAs($this->admin)
                         ->get('/admin/report/export-excel-presenze');

        // Verifica che la risposta sia un file Excel
        if ($response->status() === 200) {
            $this->assertTrue(
                str_contains($response->headers->get('content-type'), 'spreadsheet') ||
                str_contains($response->headers->get('content-type'), 'excel') ||
                str_contains($response->headers->get('content-disposition'), '.xlsx'),
                'La risposta dovrebbe essere un file Excel'
            );
        } else {
            // Se fallisce, mostriamo l'errore
            $this->fail(
                "Export Excel fallito con status {$response->status()}. " .
                "Possibile problema: libreria PhpSpreadsheet mancante o errore nel controller."
            );
        }
    }

    /** @test */
    public function export_pdf_calendario_funziona()
    {
        // Crea lezioni di test
        $sede = Sede::factory()->create();
        $utenteProfessionista = Utente::factory()->professionista()->create();
        $professionista = Professionista::factory()->create(['utente_id' => $utenteProfessionista->id]);

        Lezione::factory()->count(3)->create([
            'sede_id' => $sede->id,
            'professionista_id' => $professionista->id,
            'data_ora' => now()->addDays(2),
        ]);

        // Tenta export PDF calendario
        $response = $this->actingAs($this->admin)
                         ->get('/admin/report/export-pdf-calendario');

        // Verifica che la risposta sia un PDF
        if ($response->status() === 200) {
            $this->assertTrue(
                str_contains($response->headers->get('content-type'), 'pdf') ||
                str_contains($response->headers->get('content-disposition'), '.pdf'),
                'La risposta dovrebbe essere un file PDF'
            );
        } else {
            // Se fallisce, mostriamo l'errore
            $this->fail(
                "Export PDF fallito con status {$response->status()}. " .
                "Possibile problema: libreria Dompdf/Snappy mancante o errore nel controller."
            );
        }
    }

    /** @test */
    public function export_pdf_professionisti_funziona()
    {
        // Crea professionisti di test
        $utenteProfessionista1 = Utente::factory()->professionista()->create();
        Professionista::factory()->create(['utente_id' => $utenteProfessionista1->id]);

        $utenteProfessionista2 = Utente::factory()->professionista()->create();
        Professionista::factory()->create(['utente_id' => $utenteProfessionista2->id]);

        // Tenta export PDF professionisti
        $response = $this->actingAs($this->admin)
                         ->get('/admin/report/export-pdf-professionisti');

        if ($response->status() === 200) {
            $this->assertTrue(
                str_contains($response->headers->get('content-type'), 'pdf') ||
                str_contains($response->headers->get('content-disposition'), '.pdf'),
                'La risposta dovrebbe essere un file PDF'
            );
        } else {
            $this->fail(
                "Export PDF professionisti fallito con status {$response->status()}. " .
                "Possibile problema: libreria PDF mancante o errore nel controller."
            );
        }
    }

    /** @test */
    public function export_excel_calendario_funziona()
    {
        // Crea lezioni di test
        $sede = Sede::factory()->create();
        $utenteProfessionista = Utente::factory()->professionista()->create();
        $professionista = Professionista::factory()->create(['utente_id' => $utenteProfessionista->id]);

        Lezione::factory()->count(5)->create([
            'sede_id' => $sede->id,
            'professionista_id' => $professionista->id,
        ]);

        // Tenta export Excel calendario
        $response = $this->actingAs($this->admin)
                         ->get('/admin/report/export-excel-calendario');

        if ($response->status() === 200) {
            $this->assertTrue(
                str_contains($response->headers->get('content-type'), 'spreadsheet') ||
                str_contains($response->headers->get('content-type'), 'excel'),
                'La risposta dovrebbe essere un file Excel'
            );
        } else {
            $this->fail(
                "Export Excel calendario fallito con status {$response->status()}. " .
                "Possibile problema: libreria PhpSpreadsheet mancante."
            );
        }
    }

    /** @test */
    public function verifica_librerie_export_installate()
    {
        // Verifica che le librerie necessarie siano installate
        $phpSpreadsheetInstallata = class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class);
        $dompdfInstallata = class_exists(\Dompdf\Dompdf::class);
        $barryvdhDomPdfInstallata = class_exists(\Barryvdh\DomPDF\ServiceProvider::class);

        $errori = [];

        if (!$phpSpreadsheetInstallata) {
            $errori[] = "PhpSpreadsheet non installata. Esegui: composer require phpoffice/phpspreadsheet";
        }

        if (!$dompdfInstallata && !$barryvdhDomPdfInstallata) {
            $errori[] = "Dompdf non installata. Esegui: composer require barryvdh/laravel-dompdf";
        }

        if (!empty($errori)) {
            $this->fail(
                "Librerie mancanti per export:\n" . implode("\n", $errori)
            );
        }

        $this->assertTrue(true, "Tutte le librerie necessarie sono installate");
    }
}
