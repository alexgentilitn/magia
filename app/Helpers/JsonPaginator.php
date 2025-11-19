<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Helper per paginazione database JSON
 * Simula il comportamento di Eloquent paginate()
 */
class JsonPaginator
{
    /**
     * Pagina un array di risultati
     */
    public static function paginate(array $items, int $perPage = 15, ?int $currentPage = null, array $options = []): LengthAwarePaginator
    {
        $currentPage = $currentPage ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $items = Collection::make($items);

        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentPageItems,
            $items->count(),
            $perPage,
            $currentPage,
            array_merge([
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ], $options)
        );
    }

    /**
     * Applica withQueryString() al paginator
     */
    public static function withQueryString(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        return $paginator->appends(request()->query());
    }
}
