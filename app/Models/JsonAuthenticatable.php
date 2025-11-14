<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\JsonDatabase;
use Illuminate\Support\Collection;

/**
 * Base Authenticatable che usa JSON come backend
 * Permette login/autenticazione con database JSON
 */
abstract class JsonAuthenticatable extends Authenticatable
{
    // Disabilita timestamps automatici di Eloquent
    public $timestamps = false;

    // Disabilita connessione database
    protected $connection = null;

    /**
     * Nome tabella JSON (da definire nelle sottoclassi)
     */
    public static $jsonTable = '';

    /**
     * Override metodo newQuery per usare JSON
     */
    public function newQuery()
    {
        // Restituisce un builder custom che usa JSON
        return new JsonEloquentQueryBuilder($this);
    }

    /**
     * Override save per salvare su JSON
     */
    public function save(array $options = [])
    {
        $data = $this->attributesToArray();

        if (!isset($data['id']) || empty($data['id'])) {
            // INSERT
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            $nextId = $this->getNextId();
            $data['id'] = $nextId;

            JsonDatabase::insert(static::$jsonTable, $data);
            $this->setAttribute('id', $nextId);
        } else {
            // UPDATE
            $data['updated_at'] = date('Y-m-d H:i:s');
            JsonDatabase::update(static::$jsonTable, ['id' => $data['id']], $data);
        }

        $this->syncOriginal();
        return true;
    }

    /**
     * Override delete per eliminare da JSON
     */
    public function delete()
    {
        if (!$this->exists || !isset($this->attributes['id'])) {
            return false;
        }

        JsonDatabase::delete(static::$jsonTable, ['id' => $this->attributes['id']]);
        $this->exists = false;
        return true;
    }

    /**
     * Override create statico
     */
    public static function create(array $attributes = [])
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Override all statico
     */
    public static function all($columns = ['*'])
    {
        $data = JsonDatabase::all(static::$jsonTable);
        return static::hydrate($data);
    }

    /**
     * Override find statico
     */
    public static function find($id, $columns = ['*'])
    {
        $data = JsonDatabase::find(static::$jsonTable, ['id' => $id]);
        return $data ? static::newJsonInstance($data, true) : null;
    }

    /**
     * Override findOrFail
     */
    public static function findOrFail($id, $columns = ['*'])
    {
        $result = static::find($id, $columns);

        if (!$result) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return $result;
    }

    /**
     * Override where statico
     */
    public static function where($column, $operator = null, $value = null)
    {
        $query = new JsonEloquentQueryBuilder(new static());
        return $query->where($column, $operator, $value);
    }

    /**
     * Override count
     */
    public static function count()
    {
        return JsonDatabase::count(static::$jsonTable);
    }

    /**
     * Converte array in Collection di Models
     */
    public static function hydrate(array $items)
    {
        $models = array_map(function($item) {
            return static::newJsonInstance($item, true);
        }, $items);

        return new Collection($models);
    }

    /**
     * Crea nuova istanza del Model da array
     */
    protected static function newJsonInstance($attributes = [], $exists = false)
    {
        $model = new static((array) $attributes);
        $model->exists = $exists;
        $model->syncOriginal();
        return $model;
    }

    /**
     * Ottieni prossimo ID disponibile
     */
    protected function getNextId()
    {
        $all = JsonDatabase::all(static::$jsonTable);
        if (empty($all)) {
            return 1;
        }

        $ids = array_column($all, 'id');
        return max($ids) + 1;
    }

    /**
     * Override update statico
     */
    public function update(array $attributes = [], array $options = [])
    {
        if (!$this->exists) {
            return false;
        }

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this->save($options);
    }

    /**
     * Override getDateFormat per evitare connessione database
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * Override getConnection per restituire null senza errori
     */
    public function getConnection()
    {
        return null;
    }
}
