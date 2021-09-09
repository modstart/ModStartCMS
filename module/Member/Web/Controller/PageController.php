<?php


namespace Module\Member\Web\Controller;


use ModStart\Module\ModuleBaseController;

class PageController extends ModuleBaseController
{
    public function agreement()
    {
        return $this->view('member.agreement');
    }
}
