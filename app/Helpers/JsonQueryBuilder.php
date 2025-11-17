<?php

namespace App\Helpers;

/**
 * Query Builder per database JSON
 * Simula Eloquent query builder ma usa array
 */
class JsonQueryBuilder
{
    protected array $data = [];
    protected array $filters = [];
    protected ?string $orderBy = null;
    protected string $orderDirection = 'asc';

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Filtra per campo uguale a valore
     */
    public function where(string $field, $operator, $value = null): self
    {
        // Se sono 2 parametri, operator è il valore
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->filters[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'type' => 'and'
        ];

        return $this;
    }

    /**
     * Filtra con OR
     */
    public function orWhere(string $field, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->filters[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'type' => 'or'
        ];

        return $this;
    }

    /**
     * Ordina risultati
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->orderBy = $field;
        $this->orderDirection = strtolower($direction);
        return $this;
    }

    /**
     * Esegue la query e restituisce risultati
     */
    public function get(): array
    {
        $results = $this->data;

        // Applica filtri
        if (!empty($this->filters)) {
            $results = $this->applyFilters($results);
        }

        // Applica ordinamento
        if ($this->orderBy !== null) {
            $results = $this->applyOrdering($results);
        }

        return array_values($results);
    }

    /**
     * Pagina i risultati
     */
    public function paginate(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        $results = $this->get();
        return JsonPaginator::paginate($results, $perPage);
    }

    /**
     * Conta i risultati
     */
    public function count(): int
    {
        return count($this->get());
    }

    /**
     * Primo risultato
     */
    public function first(): ?array
    {
        $results = $this->get();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Applica i filtri ai dati
     */
    protected function applyFilters(array $data): array
    {
        $filtered = [];
        $lastType = 'and';

        foreach ($data as $item) {
            $matches = true;
            $orMatches = false;

            foreach ($this->filters as $filter) {
                $fieldValue = $item[$filter['field']] ?? null;
                $filterValue = $filter['value'];

                $currentMatch = $this->compareValues($fieldValue, $filter['operator'], $filterValue);

                if ($filter['type'] === 'and') {
                    $matches = $matches && $currentMatch;
                } else {
                    $orMatches = $orMatches || $currentMatch;
                }
            }

            // Se ci sono OR, uno solo deve matchare
            $hasOr = collect($this->filters)->contains('type', 'or');
            if ($hasOr) {
                if ($orMatches) {
                    $filtered[] = $item;
                }
            } else {
                if ($matches) {
                    $filtered[] = $item;
                }
            }
        }

        return $filtered;
    }

    /**
     * Confronta due valori in base all'operatore
     */
    protected function compareValues($value1, string $operator, $value2): bool
    {
        switch ($operator) {
            case '=':
            case '==':
                return $value1 == $value2;

            case '!=':
            case '<>':
                return $value1 != $value2;

            case '>':
                return $value1 > $value2;

            case '>=':
                return $value1 >= $value2;

            case '<':
                return $value1 < $value2;

            case '<=':
                return $value1 <= $value2;

            case 'like':
                $pattern = str_replace('%', '.*', preg_quote($value2, '/'));
                return preg_match("/^$pattern$/i", $value1) === 1;

            default:
                return false;
        }
    }

    /**
     * Applica ordinamento
     */
    protected function applyOrdering(array $data): array
    {
        usort($data, function($a, $b) {
            $aVal = $a[$this->orderBy] ?? '';
            $bVal = $b[$this->orderBy] ?? '';

            $comparison = $aVal <=> $bVal;

            return $this->orderDirection === 'desc' ? -$comparison : $comparison;
        });

        return $data;
    }
}
