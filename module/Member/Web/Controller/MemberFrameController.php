<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\Module\ModuleBaseController;

class MemberFrameController extends ModuleBaseController
{
    protected $viewMemberFrame;

    public function __construct()
    {
        list($this->viewMemberFrame, $_) = $this->viewPaths('member.frame');
        View::share('_viewMemberFrame', $this->viewMemberFrame);
    }

}
