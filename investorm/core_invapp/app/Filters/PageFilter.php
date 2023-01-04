<?php


namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PageFilter extends QueryFilters
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * @param $value
     * @return Builder
     * @version 1.0.0
     * @since 1.0
     */
    public function queryFilter($value)
    {
        if (!blank($value)) {
            return $this->builder->where(function ($query) use ($value) {
                $query->where(DB::raw('LOWER(name)'), 'like', '%'.  strtolower($value) .'%');
            });
        } else {
            return $this->builder;
        }
    }
}
