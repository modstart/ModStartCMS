<?php


namespace Module\Cms\Web\Controller;


use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;

class IndexController extends ModuleBaseController
{
    
    private $api;

    public function __construct(\Module\Cms\Api\Controller\IndexController $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $viewData = Response::tryGetData($this->api->home());
        return $this->view('cms.index', $viewData);
    }
}
