<?php


namespace Module\AigcBase\Web\Controller;


use ModStart\Module\ModuleBaseController;

class DistributionController extends ModuleBaseController
{
    public function index()
    {
        return $this->view('aigcBase.distribution');
    }

}
