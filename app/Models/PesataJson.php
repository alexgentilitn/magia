<?php

namespace App\Models;

use App\Helpers\JsonDatabase;
use App\Helpers\JsonQueryBuilder;

/**
 * Model per gestire le pesate dei clienti tramite database JSON file-based
 *
 * Gestisce lo storico delle pesate con tutti i parametri corporei
 * NO SQLite richiesto - puro PHP
 */
class PesataJson
{
    protected static string $table = 'pesate';

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
     * Ottieni tutte le pesate
     */
    public static function all(): array
    {
        return JsonDatabase::all(self::$table);
    }

    /**
     * Cerca pesate con condizioni
     */
    public static function where(array $conditions): array
    {
        return JsonDatabase::where(self::$table, $conditions);
    }

    /**
     * Trova una singola pesata
     */
    public static function find(int $id): ?array
    {
        return JsonDatabase::find(self::$table, ['id' => $id]);
    }

    /**
     * Trova pesate per cliente_id
     */
    public static function findByCliente(int $cliente_id): array
    {
        return JsonDatabase::where(self::$table, ['cliente_id' => $cliente_id]);
    }

    /**
     * Trova pesate per sede
     */
    public static function findBySede(string $sede): array
    {
        return JsonDatabase::where(self::$table, ['sede' => $sede]);
    }

    /**
     * Crea una nuova pesata
     */
    public static function create(array $data): bool
    {
        // Aggiungi timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Genera ID automatico se non fornito
        if (!isset($data['id'])) {
            $data['id'] = self::getNextId();
        }

        // Data rilevazione default
        if (!isset($data['data_rilevazione'])) {
            $data['data_rilevazione'] = date('Y-m-d');
        }

        return JsonDatabase::insert(self::$table, $data);
    }

    /**
     * Aggiorna una pesata
     */
    public static function update(int $id, array $data): bool
    {
        // Aggiorna timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return JsonDatabase::update(self::$table, ['id' => $id], $data);
    }

    /**
     * Elimina una pesata
     */
    public static function delete(int $id): bool
    {
        return JsonDatabase::delete(self::$table, ['id' => $id]);
    }

    /**
     * Conta le pesate
     */
    public static function count(): int
    {
        return JsonDatabase::count(self::$table);
    }

    /**
     * Verifica se una pesata esiste
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
        $pesate = self::all();

        if (empty($pesate)) {
            return 1;
        }

        $maxId = max(array_column($pesate, 'id'));
        return $maxId + 1;
    }

    /**
     * Cerca pesate per nome o cognome cliente
     */
    public static function search(string $query): array
    {
        $all = self::all();
        $query = strtolower($query);

        return array_filter($all, function($pesata) use ($query) {
            $nome = strtolower($pesata['nome'] ?? '');
            $cognome = strtolower($pesata['cognome'] ?? '');
            $sede = strtolower($pesata['sede'] ?? '');

            return str_contains($nome, $query)
                || str_contains($cognome, $query)
                || str_contains($sede, $query);
        });
    }

    /**
     * Ottieni pesate recenti per un cliente (ordinate per data)
     */
    public static function pesateRecenti(int $cliente_id, int $limit = 10): array
    {
        $pesate = self::findByCliente($cliente_id);

        // Ordina per data rilevazione (più recenti prima)
        usort($pesate, function($a, $b) {
            return strtotime($b['data_rilevazione'] ?? '1970-01-01') - strtotime($a['data_rilevazione'] ?? '1970-01-01');
        });

        return array_slice($pesate, 0, $limit);
    }

    /**
     * Ottieni l'ultima pesata di un cliente
     */
    public static function ultimaPesata(int $cliente_id): ?array
    {
        $recenti = self::pesateRecenti($cliente_id, 1);
        return !empty($recenti) ? $recenti[0] : null;
    }

    /**
     * Trova cliente per nome e cognome
     * Utile per associare pesate importate ai clienti esistenti
     */
    public static function trovaClientePerNomeCognome(string $nome, string $cognome): ?array
    {
        $clienti = ClienteJson::all();

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

    /**
     * Ottieni statistiche per un cliente
     */
    public static function getStatistiche(int $cliente_id): array
    {
        $pesate = self::findByCliente($cliente_id);

        if (empty($pesate)) {
            return [
                'totale_pesate' => 0,
                'peso_iniziale' => null,
                'peso_attuale' => null,
                'differenza_peso' => null,
                'bmi_iniziale' => null,
                'bmi_attuale' => null,
            ];
        }

        // Ordina per data
        usort($pesate, function($a, $b) {
            return strtotime($a['data_rilevazione'] ?? '1970-01-01') - strtotime($b['data_rilevazione'] ?? '1970-01-01');
        });

        $prima = $pesate[0];
        $ultima = end($pesate);

        $pesoIniziale = self::estraiNumero($prima['peso'] ?? 0);
        $pesoAttuale = self::estraiNumero($ultima['peso'] ?? 0);

        return [
            'totale_pesate' => count($pesate),
            'peso_iniziale' => $pesoIniziale,
            'peso_attuale' => $pesoAttuale,
            'differenza_peso' => $pesoAttuale - $pesoIniziale,
            'bmi_iniziale' => $prima['bmi'] ?? null,
            'bmi_attuale' => $ultima['bmi'] ?? null,
        ];
    }

    /**
     * Estrai numero da stringa (es: "68.75 kg" -> 68.75)
     */
    private static function estraiNumero($valore): float
    {
        if (is_numeric($valore)) {
            return (float) $valore;
        }

        $numero = preg_replace('/[^0-9.,]/', '', $valore);
        $numero = str_replace(',', '.', $numero);

        return (float) $numero;
    }
}
