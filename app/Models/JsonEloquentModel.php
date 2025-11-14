<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\JsonDatabase;
use App\Helpers\JsonQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Base Model che simula Eloquent ma usa JSON come backend
 * I controller pensano di usare MySQL ma in realtà usano JSON!
 */
abstract class JsonEloquentModel extends Model
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

/**
 * Query Builder custom per JSON
 */
class JsonEloquentQueryBuilder
{
    protected $model;
    protected $wheres = [];
    protected $orWheres = [];
    protected $orderByField = null;
    protected $orderByDirection = 'asc';
    protected $limitValue = null;
    protected $offsetValue = null;
    protected $isDistinct = false;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Where con supporto closure per OR
     */
    public function where($column, $operator = null, $value = null)
    {
        // Se $column è una closure, gestiamo OR grouping
        if ($column instanceof \Closure) {
            $column($this);
            return $this;
        }

        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [$column, $operator, $value];
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->orWheres[] = [$column, $operator, $value];
        return $this;
    }

    public function whereNotNull($column)
    {
        $this->wheres[] = [$column, 'not_null', null];
        return $this;
    }

    public function orderBy($field, $direction = 'asc')
    {
        $this->orderByField = $field;
        $this->orderByDirection = $direction;
        return $this;
    }

    public function limit($value)
    {
        $this->limitValue = $value;
        return $this;
    }

    public function offset($value)
    {
        $this->offsetValue = $value;
        return $this;
    }

    public function take($value)
    {
        return $this->limit($value);
    }

    public function skip($value)
    {
        return $this->offset($value);
    }

    public function distinct()
    {
        // Flag per distinct processing
        $this->isDistinct = true;
        return $this;
    }

    public function pluck($column)
    {
        $data = $this->get();
        $values = $data->pluck($column);

        if ($this->isDistinct ?? false) {
            return $values->unique()->values();
        }

        return $values;
    }

    /**
     * Esegui query e restituisci Collection di Models
     */
    public function get($columns = ['*'])
    {
        $tableName = $this->model::$jsonTable;
        $data = JsonDatabase::all($tableName);

        // Applica filtri WHERE
        $data = $this->applyWheres($data);

        // Applica ordinamento
        if ($this->orderByField) {
            $data = $this->applyOrdering($data);
        }

        // Applica limit/offset
        if ($this->offsetValue !== null) {
            $data = array_slice($data, $this->offsetValue);
        }
        if ($this->limitValue !== null) {
            $data = array_slice($data, 0, $this->limitValue);
        }

        return $this->model::hydrate($data);
    }

    /**
     * Conta risultati
     */
    public function count()
    {
        return $this->get()->count();
    }

    /**
     * Primo risultato
     */
    public function first($columns = ['*'])
    {
        $results = $this->limit(1)->get($columns);
        return $results->first();
    }

    /**
     * Paginazione
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);

        $total = $this->count();

        $results = $this->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get($columns);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    /**
     * Applica filtri WHERE
     */
    protected function applyWheres($data)
    {
        $filtered = [];

        foreach ($data as $item) {
            $matchesAnd = true;
            $matchesOr = false;

            // Controllo AND wheres
            foreach ($this->wheres as [$column, $operator, $value]) {
                if (!$this->compareValue($item[$column] ?? null, $operator, $value)) {
                    $matchesAnd = false;
                    break;
                }
            }

            // Controllo OR wheres
            foreach ($this->orWheres as [$column, $operator, $value]) {
                if ($this->compareValue($item[$column] ?? null, $operator, $value)) {
                    $matchesOr = true;
                }
            }

            // Include se matcha AND conditions oppure almeno un OR
            if ($matchesAnd || $matchesOr) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * Confronta valore con operatore
     */
    protected function compareValue($itemValue, $operator, $compareValue)
    {
        switch ($operator) {
            case '=':
            case '==':
                return $itemValue == $compareValue;
            case '!=':
            case '<>':
                return $itemValue != $compareValue;
            case '>':
                return $itemValue > $compareValue;
            case '>=':
                return $itemValue >= $compareValue;
            case '<':
                return $itemValue < $compareValue;
            case '<=':
                return $itemValue <= $compareValue;
            case 'like':
                $pattern = str_replace('%', '.*', preg_quote($compareValue, '/'));
                return preg_match("/^{$pattern}$/i", $itemValue) === 1;
            case 'not_null':
                return $itemValue !== null && $itemValue !== '';
            default:
                return false;
        }
    }

    /**
     * Applica ordinamento
     */
    protected function applyOrdering($data)
    {
        usort($data, function($a, $b) {
            $aVal = $a[$this->orderByField] ?? '';
            $bVal = $b[$this->orderByField] ?? '';

            $comparison = $aVal <=> $bVal;

            return $this->orderByDirection === 'desc' ? -$comparison : $comparison;
        });

        return $data;
    }
}
