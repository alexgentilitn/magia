<?php

namespace App\Models;

/**
 * Relazioni JSON che simulano Eloquent Relations
 * Queste classi permettono ai controller di usare ->ruolo, ->cliente, ecc.
 * mantenendo compatibilità con Eloquent
 */

class JsonBelongsTo
{
    protected $parent;
    protected $related;
    protected $foreignKey;
    protected $ownerKey;
    protected $cachedResult = null;

    public function __construct($parent, $related, $foreignKey, $ownerKey = 'id')
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
    }

    /**
     * Ottieni il risultato della relazione
     */
    public function getResults()
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }

        $foreignKeyValue = $this->parent->getAttribute($this->foreignKey);

        if (!$foreignKeyValue) {
            return null;
        }

        $this->cachedResult = $this->related::find($foreignKeyValue);
        return $this->cachedResult;
    }

    /**
     * Magic method per accesso diretto
     */
    public function __get($key)
    {
        $result = $this->getResults();
        return $result ? $result->$key : null;
    }

    /**
     * Rendi callable (per compatibilità con Blade)
     */
    public function __invoke()
    {
        return $this->getResults();
    }
}

class JsonHasOne
{
    protected $parent;
    protected $related;
    protected $foreignKey;
    protected $localKey;
    protected $cachedResult = null;

    public function __construct($parent, $related, $foreignKey, $localKey = 'id')
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function getResults()
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }

        $localKeyValue = $this->parent->getAttribute($this->localKey);

        if (!$localKeyValue) {
            return null;
        }

        $this->cachedResult = $this->related::where($this->foreignKey, $localKeyValue)->first();
        return $this->cachedResult;
    }

    public function __get($key)
    {
        $result = $this->getResults();
        return $result ? $result->$key : null;
    }

    public function __invoke()
    {
        return $this->getResults();
    }
}

class JsonHasMany
{
    protected $parent;
    protected $related;
    protected $foreignKey;
    protected $localKey;
    protected $cachedResult = null;

    public function __construct($parent, $related, $foreignKey, $localKey = 'id')
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function getResults()
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }

        $localKeyValue = $this->parent->getAttribute($this->localKey);

        if (!$localKeyValue) {
            return collect([]);
        }

        $this->cachedResult = $this->related::where($this->foreignKey, $localKeyValue)->get();
        return $this->cachedResult;
    }

    public function count()
    {
        return $this->getResults()->count();
    }

    public function __invoke()
    {
        return $this->getResults();
    }
}
