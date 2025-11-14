<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * Model Sushi per database locale file-based
 *
 * Questo model usa Sushi per leggere dati da file JSON
 * invece che da MySQL. Perfetto per sviluppo locale!
 */
class ClienteLocal extends Model
{
    use Sushi;

    /**
     * Path al file JSON con i dati
     *
     * Il file sarà in: database/sushi/clienti.json
     */
    public function getRows()
    {
        $jsonPath = database_path('sushi/clienti.json');

        if (!file_exists($jsonPath)) {
            return [];
        }

        $json = file_get_contents($jsonPath);
        return json_decode($json, true) ?? [];
    }

    /**
     * Schema delle colonne
     */
    protected $schema = [
        'id' => 'integer',
        'nome' => 'string',
        'cognome' => 'string',
        'email' => 'string',
        'telefono' => 'string',
        'data_nascita' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
