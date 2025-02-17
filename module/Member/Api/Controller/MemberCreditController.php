<?php


namespace Module\Member\Api\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberCreditUtil;

class MemberCreditController extends ModuleBaseController implements MemberLoginCheck
{
    public function get()
    {
        $credit = MemberCreditUtil::get(MemberUser::id());
        return Response::generateSuccessData([
            'total' => $credit ? $credit['total'] : 0,
            'freezeTotal' => $credit ? $credit['freezeTotal'] : 0,
        ]);
    }


    public function log()
    {
        $input = InputPackage::buildFromInput();
        $option = [];
        $searchInput = $input->getJsonAsInput('search');
        $type = $searchInput->getTrimString('type');
        switch ($type) {
            case 'income':
                $option['whereOperate'] = ['change', '>', '0'];
                break;
            case 'payout':
                $option['whereOperate'] = ['change', '<', '0'];
                break;
        }
        $paginateData = MemberCreditUtil::paginateLog(
            MemberUser::id(),
            $input->getPage(),
            $input->getPageSize(),
            $option
        );
        return Response::generateSuccessPaginate(
            $input->getPage(),
            $input->getPageSize(),
            $paginateData
        );
    }
}
