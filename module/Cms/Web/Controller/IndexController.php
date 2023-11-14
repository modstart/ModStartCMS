<?php


namespace Module\Cms\Web\Controller;

use ModStart\Module\ModuleBaseController;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        return $this->view('cms.index', [
            'pageTitle' => modstart_config('siteName') . ' | ' . modstart_config('siteSlogan'),
            'pageKeywords' => modstart_config('siteKeywords'),
            'pageDescription' => modstart_config('siteDescription'),
        ]);
    }
}
