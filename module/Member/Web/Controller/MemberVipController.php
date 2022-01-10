<?php


namespace Module\Member\Web\Controller;

use ModStart\Core\Exception\BizException;
use ModStart\Module\ModuleBaseController;
use ModStart\Module\ModuleManager;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberVipUtil;

class MemberVipController extends ModuleBaseController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberVipController */
    private $api;

    public function __construct()
    {
        BizException::throwsIf('缺少 PayCenter 模块', !ModuleManager::isModuleEnabled('PayCenter'));
        $this->api = app(\Module\Member\Api\Controller\MemberVipController::class);
    }

    public function index()
    {
        return $this->view('memberVip.index', [
            'memberVips' => MemberVipUtil::all(),
        ]);
    }

}
