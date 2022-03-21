<?php


namespace Module\Member\Web\Controller;

use ModStart\Core\Exception\BizException;
use ModStart\Module\ModuleBaseController;
use ModStart\Module\ModuleManager;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberVipUtil;

class MemberVipController extends ModuleBaseController
{
    /** @var \Module\Member\Api\Controller\MemberVipController */
    private $api;

    public function index()
    {
        BizException::throwsIf('缺少 PayCenter 模块', !ModuleManager::isModuleEnabled('PayCenter'));
        $this->api = app(\Module\Member\Api\Controller\MemberVipController::class);
        return $this->view('memberVip.index', [
            'memberVips' => MemberVipUtil::all(),
        ]);
    }

}
