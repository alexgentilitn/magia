<?php

/**
 * Script per convertire i model da MySQL a JSON
 */

$models = [
    'Programma' => 'programmi',
    'Lezione' => 'lezioni',
    'Pagamento' => 'pagamenti',
    'Professionista' => 'professionisti',
    'Sede' => 'sedi',
    'Ruolo' => 'ruoli',
];

foreach ($models as $modelName => $tableName) {
    $filePath = __DIR__ . "/app/Models/{$modelName}.php";

    if (!file_exists($filePath)) {
        echo "❌ File non trovato: {$filePath}\n";
        continue;
    }

    // Leggi il file
    $content = file_get_contents($filePath);

    // Backup del file originale
    $backupPath = __DIR__ . "/app/Models/{$modelName}.mysql.backup.php";
    file_put_contents($backupPath, $content);
    echo "✅ Backup creato: {$modelName}.mysql.backup.php\n";

    // Conversioni
    $modifications = [];

    // 1. Cambia extends Model in extends JsonEloquentModel
    if (strpos($content, 'extends Model') !== false) {
        $content = str_replace('extends Model', 'extends JsonEloquentModel', $content);
        $modifications[] = "extends JsonEloquentModel";
    }

    // 2. Rimuovi SoftDeletes dal use (ma mantieni HasFactory)
    if (strpos($content, 'use HasFactory, SoftDeletes;') !== false) {
        $content = str_replace('use HasFactory, SoftDeletes;', 'use HasFactory;', $content);
        $modifications[] = "rimosso SoftDeletes";
    } elseif (strpos($content, 'use SoftDeletes, HasFactory;') !== false) {
        $content = str_replace('use SoftDeletes, HasFactory;', 'use HasFactory;', $content);
        $modifications[] = "rimosso SoftDeletes";
    } elseif (strpos($content, 'use HasFactory;') === false && strpos($content, 'use SoftDeletes;') !== false) {
        $content = str_replace('use SoftDeletes;', '', $content);
        $modifications[] = "rimosso SoftDeletes";
    }

    // 3. Rimuovi use Illuminate\Database\Eloquent\SoftDeletes;
    $content = preg_replace('/use Illuminate\\\\Database\\\\Eloquent\\\\SoftDeletes;\s*\n/', '', $content);

    // 4. Aggiungi $jsonTable dopo protected $table
    if (preg_match('/protected \$table = \'([^\']+)\';/', $content, $matches)) {
        $tableValue = $matches[1];
        $oldLine = "protected \$table = '{$tableValue}';";
        $newLine = "protected \$table = '{$tableValue}';\n    public static \$jsonTable = '{$tableValue}';";

        $content = str_replace($oldLine, $newLine, $content);
        $modifications[] = "aggiunto \$jsonTable = '{$tableValue}'";
    }

    // 5. Aggiungi commento Backend JSON all'inizio del docblock
    $content = preg_replace(
        '/(\/\*\*\s*\n\s*\* Model: ' . $modelName . ')/',
        "/**\n * Model: {$modelName}\n * Backend: JSON file-based (database/jsondb/{$tableName}.json)",
        $content
    );
    $modifications[] = "aggiunto commento Backend JSON";

    // Salva il file convertito
    file_put_contents($filePath, $content);

    echo "✅ Convertito {$modelName}:\n";
    foreach ($modifications as $mod) {
        echo "   - {$mod}\n";
    }
    echo "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ CONVERSIONE COMPLETATA!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
