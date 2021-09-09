<?php


namespace Module\Member\Web\Controller;

use ModStart\Module\ModuleBaseController;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberVipUtil;

class MemberVipController extends ModuleBaseController implements MemberLoginCheck
{
    
    private $api;

    public function __construct()
    {
        $this->api = app(\Module\Member\Api\Controller\MemberVipController::class);
    }

    public function index()
    {
        return $this->view('memberVip.index', [
            'memberVips' => MemberVipUtil::all(),
        ]);
    }

}