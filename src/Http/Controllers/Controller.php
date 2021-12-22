<?php

namespace Jdlx\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Jdlx\Http\Controllers\Traits\CanFilterSortAndPage;

/**
 * Class Controller
 * https://specs.openstack.org/openstack/api-wg/guidelines/pagination_filter_sort.html
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, CanFilterSortAndPage;
}
