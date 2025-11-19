<?php

namespace App\Models;

use App\Helpers\JsonDatabase;

/**
 * Model JSON per professionisti
 * Usa JsonDatabase helper (jajo/jsondb)
 */
class ProfessionistaJson
{
    protected static string $table = 'professionisti';

    public static function all(): array
    {
        return JsonDatabase::all(self::$table);
    }

    public static function where(array $conditions): array
    {
        return JsonDatabase::where(self::$table, $conditions);
    }

    public static function find(int $id): ?array
    {
        return JsonDatabase::find(self::$table, ['id' => $id]);
    }

    public static function create(array $data): bool
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!isset($data['id'])) {
            $data['id'] = self::getNextId();
        }

        return JsonDatabase::insert(self::$table, $data);
    }

    public static function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return JsonDatabase::update(self::$table, ['id' => $id], $data);
    }

    public static function delete(int $id): bool
    {
        return JsonDatabase::delete(self::$table, ['id' => $id]);
    }

    public static function count(): int
    {
        return JsonDatabase::count(self::$table);
    }

    public static function exists(int $id): bool
    {
        return self::find($id) !== null;
    }

    private static function getNextId(): int
    {
        $records = self::all();
        if (empty($records)) {
            return 1;
        }
        $maxId = max(array_column($records, 'id'));
        return $maxId + 1;
    }

    public static function initTable(): bool
    {
        if (!JsonDatabase::tableExists(self::$table)) {
            return JsonDatabase::createTable(self::$table);
        }
        return true;
    }
}
