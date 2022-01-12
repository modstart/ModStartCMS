<?php


namespace Module\Cms\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;

/**
 * Class CatController
 * @package Module\Cms\Api\Controller
 */
class CatController extends Controller
{
    /**
     * @return array
     */
    public function get()
    {
        return Response::generateSuccessData(null);
    }
}