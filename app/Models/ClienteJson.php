<?php

namespace App\Models;

use App\Helpers\JsonDatabase;
use App\Helpers\JsonQueryBuilder;

/**
 * Model per gestire i clienti tramite database JSON file-based
 *
 * Usa JsonDatabase helper (jajo/jsondb)
 * NO SQLite richiesto - puro PHP
 */
class ClienteJson
{
    protected static string $table = 'clienti';

    /**
     * Inizio query builder (simula Eloquent)
     */
    public static function query(): JsonQueryBuilder
    {
        $data = JsonDatabase::all(self::$table);
        return new JsonQueryBuilder($data);
    }

    /**
     * Alias per orderBy
     */
    public static function orderBy(string $field, string $direction = 'asc'): JsonQueryBuilder
    {
        return self::query()->orderBy($field, $direction);
    }

    /**
     * Ottieni tutti i clienti
     */
    public static function all(): array
    {
        return JsonDatabase::all(self::$table);
    }

    /**
     * Cerca clienti con condizioni
     */
    public static function where(array $conditions): array
    {
        return JsonDatabase::where(self::$table, $conditions);
    }

    /**
     * Trova un singolo cliente
     */
    public static function find(int $id): ?array
    {
        return JsonDatabase::find(self::$table, ['id' => $id]);
    }

    /**
     * Trova un cliente per email
     */
    public static function findByEmail(string $email): ?array
    {
        return JsonDatabase::find(self::$table, ['email' => $email]);
    }

    /**
     * Crea un nuovo cliente
     */
    public static function create(array $data): bool
    {
        // Standardizza i campi per match con la struttura esistente
        $data = self::standardizeFields($data);

        // Aggiungi timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Genera ID automatico se non fornito
        if (!isset($data['id'])) {
            $data['id'] = self::getNextId();
        }

        return JsonDatabase::insert(self::$table, $data);
    }

    /**
     * Standardizza i campi del cliente per match con la struttura JSON
     * IMPORTANTE: L'ordine dei campi DEVE matchare esattamente il primo record
     */
    private static function standardizeFields(array $data): array
    {
        // Ordine ESATTO dei campi come nel primo record del database
        $template = [
            'id' => null,  // Sarà sovrascritto
            'utente_id' => null,
            'nome' => '',
            'cognome' => '',
            'codice_fiscale' => null,
            'indirizzo' => null,
            'citta' => null,
            'provincia' => null,
            'cap' => null,
            'nazione' => null,
            'telefono_fisso' => null,
            'telefono_mobile' => null,
            'email' => '',
            'data_nascita' => null,
            'sesso' => null,
            'obiettivi_personali' => null,
            'note_mediche' => null,
            'certificato_medico' => false,
            'programma_attuale' => null,
            'data_iscrizione' => date('Y-m-d'),
            'stato_programma' => null,
            'consenso_privacy' => false,
            'consenso_marketing' => false,
            'consenso_foto' => false,
            'note_interne' => null,
            'stato_cliente' => 'attivo',
            'codice_cliente' => null,
            'codice_referral' => null,
            'created_at' => null,  // Sarà sovrascritto
            'updated_at' => null,  // Sarà sovrascritto
        ];

        // Merge mantenendo l'ordine del template
        $result = [];
        foreach ($template as $key => $defaultValue) {
            $result[$key] = $data[$key] ?? $defaultValue;
        }

        return $result;
    }

    /**
     * Aggiorna un cliente
     */
    public static function update(int $id, array $data): bool
    {
        // Aggiorna timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return JsonDatabase::update(self::$table, ['id' => $id], $data);
    }

    /**
     * Elimina un cliente
     */
    public static function delete(int $id): bool
    {
        return JsonDatabase::delete(self::$table, ['id' => $id]);
    }

    /**
     * Conta i clienti
     */
    public static function count(): int
    {
        return JsonDatabase::count(self::$table);
    }

    /**
     * Verifica se un cliente esiste
     */
    public static function exists(int $id): bool
    {
        return self::find($id) !== null;
    }

    /**
     * Ottieni l'ID successivo disponibile
     */
    private static function getNextId(): int
    {
        $clienti = self::all();

        if (empty($clienti)) {
            return 1;
        }

        $maxId = max(array_column($clienti, 'id'));
        return $maxId + 1;
    }

    /**
     * Cerca clienti per nome o cognome
     */
    public static function search(string $query): array
    {
        $all = self::all();
        $query = strtolower($query);

        return array_filter($all, function($cliente) use ($query) {
            $nome = strtolower($cliente['nome'] ?? '');
            $cognome = strtolower($cliente['cognome'] ?? '');
            $email = strtolower($cliente['email'] ?? '');

            return str_contains($nome, $query)
                || str_contains($cognome, $query)
                || str_contains($email, $query);
        });
    }

    /**
     * Trova cliente per nome e cognome
     */
    public static function findByNomeCognome(string $nome, string $cognome): ?array
    {
        $clienti = self::all();

        foreach ($clienti as $cliente) {
            $nomeCliente = strtolower(trim($cliente['nome'] ?? ''));
            $cognomeCliente = strtolower(trim($cliente['cognome'] ?? ''));
            $nomeInput = strtolower(trim($nome));
            $cognomeInput = strtolower(trim($cognome));

            if ($nomeCliente === $nomeInput && $cognomeCliente === $cognomeInput) {
                return $cliente;
            }
        }

        return null;
    }

    /**
     * Inizializza la tabella se non esiste
     */
    public static function initTable(): bool
    {
        if (!JsonDatabase::tableExists(self::$table)) {
            return JsonDatabase::createTable(self::$table);
        }
        return true;
    }
}
