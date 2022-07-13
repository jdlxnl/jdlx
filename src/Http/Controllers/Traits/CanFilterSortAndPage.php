<?php

namespace Jdlx\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Jdlx\Http\Pagination\Paginator;

trait CanFilterSortAndPage
{
    protected static $OPPS = [
        'eq' => '=',
        'neq' => '<>',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'like' => 'like',
        'json' => 'json'
    ];

    /**
     * @param Request $request
     * @param Builder $items
     * @param array $withWhiteList
     * @return array|false|string[]
     */
    public function getRelationshipFields(Request $request, $withWhiteList = [])
    {
        $with = $request->get("with", "");
        return array_filter(explode(",", $with), function ($field) use ($withWhiteList) {
            return in_array($field, $withWhiteList);
        });
    }


    /**
     * @param Request $request
     * @param Builder $items
     * @return LengthAwarePaginator
     */
    public function filterSortAndPage(Request $request, Builder $items, $filterWhiteList = [])
    {
        $this->sort($request, $items);
        $this->applyFilters($request, $items, $filterWhiteList);
        return $this->limit($request, $items);
    }

    /**
     * @param Request $request
     * @param Builder $items
     * @return LengthAwarePaginator
     */
    protected function limit(Request $request, Builder $items)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        return new Paginator(
            $items,
            $items->count(),
            $limit,
            $page, [
            'path' => $request->path()
        ]);
    }

    protected function applyFilters(Request $request, Builder $items, $filterWhiteList)
    {
        $filters = $request->get("filter", []);
        $parsedFilters = $this->collapseFilter($filters);
        foreach ($parsedFilters as $field => $val) {
            $this->applyFilter($items, $field, $val);
        }
    }

    protected function collapseFilter($filters, $prefix = "")
    {
        $merged = [];
        foreach ($filters as $field => $val) {
            if (is_array($val)) {
                $merged = array_merge($merged, $this->collapseFilter($val, $prefix . $field . "."));
            } else {
                $field = $prefix . $field;
                $merged[$field] = $val;
            }
        }
        return $merged;
    }

    protected function applyFilter(Builder $items, $field, $value)
    {
        $split = explode(":", $value);
        if (sizeof($split) === 1) {
            array_unshift($split, "eq");
        }

        list($opp, $val) = $split;
        switch ($opp) {
            case "eq":
            case "neq":
            case "gt":
            case "gte":
            case "lt":
            case "lte":
            case "like":
                if ($val === "true" || $val === "false") $val = ($val === "true");
                $items->where($field, self::$OPPS[$opp], $val);
                break;
            case "in":
                $items->whereIn($field, explode(",", $val));
                break;
            case "nin":
                $items->whereNotIn($field, explode(",", $val));
                break;
            case "json":
                list($field, $prop) = explode("->", $field);
                $items->whereRaw("LOWER({$field}->'$.{$prop}') like ?", '%' . strtolower($val) . '%');
                break;
        }
    }

    protected function sort(Request $request, Builder $items)
    {
        $sortField = $request->get("sort", false);
        if (!$sortField) {
            return;
        }

        $fields = explode(",", $sortField);
        foreach ($fields as $sortField) {
            list($field, $dir) = explode(":", $sortField);
            $items->orderBy($field, $dir === "asc" ? 'asc' : 'desc');
        }
    }
}
