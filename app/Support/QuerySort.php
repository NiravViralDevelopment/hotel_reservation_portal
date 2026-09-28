<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QuerySort
{
    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  array<string, string>  $allowed  map of request key => db column
     * @return Builder<\Illuminate\Database\Eloquent\Model>
     */
    public static function apply(Builder $query, Request $request, array $allowed, string $default, string $defaultDir = 'asc'): Builder
    {
        $sort = $request->string('sort')->toString();
        $dir = strtolower($request->string('dir')->toString()) === 'desc' ? 'desc' : 'asc';

        if ($sort === '' || ! array_key_exists($sort, $allowed)) {
            $sort = $default;
            $dir = strtolower($defaultDir) === 'desc' ? 'desc' : 'asc';
        }

        return $query->reorder()->orderBy($allowed[$sort], $dir);
    }

    /**
     * @return array{sort: string, dir: string}
     */
    public static function current(Request $request, string $default, string $defaultDir = 'asc'): array
    {
        $sort = $request->string('sort')->toString();
        $dir = strtolower($request->string('dir')->toString()) === 'desc' ? 'desc' : 'asc';

        if ($sort === '') {
            $sort = $default;
            $dir = strtolower($defaultDir) === 'desc' ? 'desc' : 'asc';
        }

        return ['sort' => $sort, 'dir' => $dir];
    }
}
