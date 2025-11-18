<?php

/**
 * Script per generare automaticamente tutti i Models JSON
 * Basato sul pattern di ClienteJson.php
 */

$models = [
    'Utente' => 'utenti',
    'Lezione' => 'lezioni',
    'Programma' => 'programmi',
    'Pagamento' => 'pagamenti',
    'Professionista' => 'professionisti',
    'Sede' => 'sedi',
    'Ruolo' => 'ruoli',
];

foreach ($models as $modelName => $tableName) {
    $filename = "app/Models/{$modelName}Json.php";

    $content = <<<PHP
<?php

namespace App\Models;

use App\Helpers\JsonDatabase;

/**
 * Model JSON per {$tableName}
 * Usa JsonDatabase helper (jajo/jsondb)
 */
class {$modelName}Json
{
    protected static string \$table = '{$tableName}';

    public static function all(): array
    {
        return JsonDatabase::all(self::\$table);
    }

    public static function where(array \$conditions): array
    {
        return JsonDatabase::where(self::\$table, \$conditions);
    }

    public static function find(int \$id): ?array
    {
        return JsonDatabase::find(self::\$table, ['id' => \$id]);
    }

    public static function create(array \$data): bool
    {
        \$data['created_at'] = date('Y-m-d H:i:s');
        \$data['updated_at'] = date('Y-m-d H:i:s');

        if (!isset(\$data['id'])) {
            \$data['id'] = self::getNextId();
        }

        return JsonDatabase::insert(self::\$table, \$data);
    }

    public static function update(int \$id, array \$data): bool
    {
        \$data['updated_at'] = date('Y-m-d H:i:s');
        return JsonDatabase::update(self::\$table, ['id' => \$id], \$data);
    }

    public static function delete(int \$id): bool
    {
        return JsonDatabase::delete(self::\$table, ['id' => \$id]);
    }

    public static function count(): int
    {
        return JsonDatabase::count(self::\$table);
    }

    public static function exists(int \$id): bool
    {
        return self::find(\$id) !== null;
    }

    private static function getNextId(): int
    {
        \$records = self::all();
        if (empty(\$records)) {
            return 1;
        }
        \$maxId = max(array_column(\$records, 'id'));
        return \$maxId + 1;
    }

    public static function initTable(): bool
    {
        if (!JsonDatabase::tableExists(self::\$table)) {
            return JsonDatabase::createTable(self::\$table);
        }
        return true;
    }
}

PHP;

    file_put_contents($filename, $content);
    echo "✅ Creato: $filename\n";
}

echo "\n🎉 Tutti i Models JSON creati!\n";
