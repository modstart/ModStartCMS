<?php


namespace Module\Cms\Web\Controller;

use ModStart\Module\ModuleBaseController;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        return $this->view('cms.index');
    }
}
