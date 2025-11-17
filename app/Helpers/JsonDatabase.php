<?php

namespace App\Helpers;

use Jajo\JSONDB;

/**
 * Helper per gestire il database JSON file-based
 * Usa la libreria jajo/jsondb (puro PHP, no SQLite)
 */
class JsonDatabase
{
    private static ?JSONDB $instance = null;
    private static ?string $dbPath = null;

    /**
     * Inizializza il path del database
     */
    private static function init(): void
    {
        if (self::$dbPath === null) {
            self::$dbPath = database_path('jsondb');

            // Crea la directory se non esiste
            if (!is_dir(self::$dbPath)) {
                mkdir(self::$dbPath, 0755, true);
            }
        }
    }

    /**
     * Ottiene l'istanza del database JSON
     */
    public static function db(): JSONDB
    {
        self::init();

        if (self::$instance === null) {
            self::$instance = new JSONDB(self::$dbPath);
        }

        return self::$instance;
    }

    /**
     * SELECT * da una tabella
     */
    public static function all(string $table): array
    {
        try {
            return self::db()->select('*')
                ->from("$table.json")
                ->get();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * SELECT con WHERE
     */
    public static function where(string $table, array $conditions): array
    {
        try {
            return self::db()->select('*')
                ->from("$table.json")
                ->where($conditions)
                ->get();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Trova un singolo record
     */
    public static function find(string $table, array $conditions): ?array
    {
        $results = self::where($table, $conditions);
        return !empty($results) ? $results[0] : null;
    }

    /**
     * INSERT
     */
    public static function insert(string $table, array $data): bool
    {
        try {
            self::db()->insert("$table.json", $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * UPDATE
     */
    public static function update(string $table, array $conditions, array $data): bool
    {
        try {
            self::db()->update($data)
                ->from("$table.json")
                ->where($conditions)
                ->trigger();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * DELETE
     */
    public static function delete(string $table, array $conditions): bool
    {
        try {
            self::db()->delete()
                ->from("$table.json")
                ->where($conditions)
                ->trigger();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Conta i record
     */
    public static function count(string $table, array $conditions = []): int
    {
        if (empty($conditions)) {
            return count(self::all($table));
        }

        return count(self::where($table, $conditions));
    }

    /**
     * Verifica se esiste un file tabella
     */
    public static function tableExists(string $table): bool
    {
        self::init();
        return file_exists(self::$dbPath . "/$table.json");
    }

    /**
     * Crea una nuova tabella vuota
     */
    public static function createTable(string $table): bool
    {
        self::init();
        $path = self::$dbPath . "/$table.json";

        if (file_exists($path)) {
            return false;
        }

        return file_put_contents($path, json_encode([], JSON_PRETTY_PRINT)) !== false;
    }
}
