<?php


namespace Module\Member\Web\Controller;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
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
        BizException::throwsIf('缺少 PayCenter 模块', !modstart_module_enabled('PayCenter'));
        $this->api = app(\Module\Member\Api\Controller\MemberVipController::class);
        $view = 'memberVip.index';
        $input = InputPackage::buildFromInput();
        $dialog = $input->getInteger('dialog');
        if ($dialog) {
            $view = 'memberVip.indexDialog';
            $this->shareDialogPageViewFrame();
        }
        return $this->view($view, [
            'memberVips' => MemberVipUtil::all(),
            'memberVipRights' => MemberVipUtil::rights(),
        ]);
    }

}
