<?php


namespace App\Web\Controller;

class IndexController extends BaseController
{

    public function index(\Module\Cms\Web\Controller\IndexController $cms)
    {
        return $cms->index();
    }
}
