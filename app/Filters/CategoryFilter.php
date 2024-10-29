<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;


class CategoryFilter implements Filter{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('categories', function (Builder $query) use ($value) {
            $query->where('category', $value);
        });
    }
}