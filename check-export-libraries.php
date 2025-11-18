<?php

/**
 * Script standalone per verificare librerie export PDF/Excel
 * Esegui con: php check-export-libraries.php
 */

require __DIR__ . '/vendor/autoload.php';

echo "=================================================\n";
echo "VERIFICA LIBRERIE EXPORT PDF/EXCEL\n";
echo "=================================================\n\n";

$problemi = [];
$successi = [];

// Verifica PhpSpreadsheet (per Excel)
echo "1. PhpSpreadsheet (Excel Export)\n";
echo "   Controllando classe: \\PhpOffice\\PhpSpreadsheet\\Spreadsheet\n";
if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
    echo "   ✅ INSTALLATA\n\n";
    $successi[] = "PhpSpreadsheet";

    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Test');
        echo "   ✅ Test creazione Spreadsheet: OK\n\n";
    } catch (\Exception $e) {
        echo "   ⚠️  Test creazione fallito: " . $e->getMessage() . "\n\n";
        $problemi[] = "PhpSpreadsheet installata ma errore al test: " . $e->getMessage();
    }
} else {
    echo "   ❌ NON INSTALLATA\n";
    echo "   Comando per installare:\n";
    echo "   composer require phpoffice/phpspreadsheet\n\n";
    $problemi[] = "PhpSpreadsheet NON installata";
}

// Verifica Dompdf
echo "2. Dompdf (PDF Export)\n";
echo "   Controllando classe: \\Dompdf\\Dompdf\n";
if (class_exists(\Dompdf\Dompdf::class)) {
    echo "   ✅ INSTALLATA\n\n";
    $successi[] = "Dompdf";

    try {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml('<h1>Test</h1>');
        echo "   ✅ Test creazione PDF: OK\n\n";
    } catch (\Exception $e) {
        echo "   ⚠️  Test creazione fallito: " . $e->getMessage() . "\n\n";
        $problemi[] = "Dompdf installata ma errore al test: " . $e->getMessage();
    }
} else {
    echo "   ❌ NON INSTALLATA\n";
    echo "   Comando per installare:\n";
    echo "   composer require dompdf/dompdf\n\n";
    $problemi[] = "Dompdf NON installata";
}

// Verifica Barryvdh DomPDF (wrapper Laravel)
echo "3. Barryvdh DomPDF (Laravel wrapper per PDF)\n";
echo "   Controllando classe: \\Barryvdh\\DomPDF\\ServiceProvider\n";
if (class_exists(\Barryvdh\DomPDF\ServiceProvider::class)) {
    echo "   ✅ INSTALLATA\n\n";
    $successi[] = "Barryvdh DomPDF";
} else {
    echo "   ❌ NON INSTALLATA\n";
    echo "   Comando per installare:\n";
    echo "   composer require barryvdh/laravel-dompdf\n\n";
    $problemi[] = "Barryvdh DomPDF NON installata";
}

// Verifica Maatwebsite Excel (alternativa per Excel)
echo "4. Maatwebsite Excel (Laravel wrapper per Excel - opzionale)\n";
echo "   Controllando classe: \\Maatwebsite\\Excel\\ExcelServiceProvider\n";
if (class_exists(\Maatwebsite\Excel\ExcelServiceProvider::class)) {
    echo "   ✅ INSTALLATA\n\n";
    $successi[] = "Maatwebsite Excel";
} else {
    echo "   ⚠️  NON INSTALLATA (opzionale)\n";
    echo "   Comando per installare:\n";
    echo "   composer require maatwebsite/excel\n\n";
}

// Riepilogo
echo "=================================================\n";
echo "RIEPILOGO\n";
echo "=================================================\n\n";

if (!empty($successi)) {
    echo "✅ LIBRERIE INSTALLATE:\n";
    foreach ($successi as $lib) {
        echo "   - $lib\n";
    }
    echo "\n";
}

if (!empty($problemi)) {
    echo "❌ PROBLEMI TROVATI:\n";
    foreach ($problemi as $problema) {
        echo "   - $problema\n";
    }
    echo "\n";

    echo "AZIONI CONSIGLIATE:\n";
    if (in_array("PhpSpreadsheet NON installata", $problemi)) {
        echo "   1. composer require phpoffice/phpspreadsheet\n";
    }
    if (in_array("Dompdf NON installata", $problemi)) {
        echo "   2. composer require dompdf/dompdf\n";
    }
    if (in_array("Barryvdh DomPDF NON installata", $problemi)) {
        echo "   3. composer require barryvdh/laravel-dompdf\n";
    }
    echo "\n";

    exit(1);
} else {
    echo "✅ Tutte le librerie necessarie sono installate!\n\n";
    exit(0);
}
