<?php


namespace Jdlx\Http\Pagination;

/**
 * @OA\Schema(
 *      description="Paging information",
 *      title="Paging",
 *      readOnly=true,
 *
 *      @OA\Property(
 *       property="pageIndex",
 *       description="The current page",
 *       type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="currentItemCount",
 *          description="The number of items in current result",
 *          type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="totalItems",
 *          description="The total available results",
 *          type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="itemsPerPage",
 *          description="Result count per page",
 *          type="integer"
 *      ),
 *
 *      @OA\Property(
 *       property="startIndex",
 *       description="The index of the first item of the current result",
 *       type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="endIndex",
 *          description="The last index in the current result",
 *          type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="lastPageIndex",
 *          description="The last available page",
 *          type="integer"
 *      ),
 *
 *      @OA\Property(
 *          property="path",
 *          description="The base path of the request",
 *          type="string",
 *          format="uri"
 *      ),
 *
 *      @OA\Property(
 *          property="firstLink",
 *          description="The url to retrieve the previous items",
 *          type="string",
 *          format="uri"
 *      ),
 *
 *      @OA\Property(
 *          property="previousLink",
 *          description="The url to retrieve the previous items",
 *          type="string",
 *          format="uri"
 *      ),
 *
 *      @OA\Property(
 *          property="nextLink",
 *          description="The url to retrieve more items",
 *          type="string",
 *          format="uri"
 *      ),
 *
 *      @OA\Property(
 *          property="lastLink",
 *          description="The url to retrieve the last items",
 *          type="string",
 *          format="uri"
 *      ),
 *  )
 */

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Paginator extends \Illuminate\Pagination\LengthAwarePaginator
{
    /**
     * Create a new paginator instance.
     *
     * @param mixed $items
     * @param int $total
     * @param int $perPage
     * @param int|null $currentPage
     * @param array $options (path, query, fragment, pageName)
     * @return void
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        $offset = $perPage * ($currentPage - 1);
        if ($items instanceof Relation) {
            $items = $items->skip($offset)->take($perPage)->get();
        }
        if ($items instanceof EloquentBuilder || $items instanceof QueryBuilder) {
            $items = $items->skip($offset)->take($perPage)->get();
        }
        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }

    public function items()
    {
        return $this->items;
    }

    public function toArray()
    {
        return [
            'currentItemCount' => $this->count(),
            'totalItems' => $this->total(),
            'itemsPerPage' => $this->perPage(),
            'pageIndex' => $this->currentPage(),
            'startIndex' => $this->firstItem(),
            'endIndex' => $this->lastItem(),
            'lastPageIndex' => $this->lastPage(),
            'path' => $this->path,
            'firstLink' => $this->url(1),
            'previousLink' => $this->previousPageUrl(),
            'nextLink' => $this->nextPageUrl(),
            'lastLink' => $this->url($this->lastPage()),
        ];
    }
}
