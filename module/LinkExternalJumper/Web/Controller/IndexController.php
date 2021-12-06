<?php

namespace Module\LinkExternalJumper\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        $input = InputPackage::buildFromInput();
        $target = $input->getTrimString('target');
        if (empty($target)) {
            return Response::redirect(modstart_web_url(''));
        }
        return $this->view('linkExternalJumper.index', [
            'target' => $target,
        ]);
    }
}
