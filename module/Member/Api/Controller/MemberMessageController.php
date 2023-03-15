<?php


namespace Module\Member\Api\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberMessageUtil;

class MemberMessageController extends ModuleBaseController implements MemberLoginCheck
{
    public function paginate()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getInteger('page');
        $pageSize = 10;
        $option = [
            'search' => [],
            'order' => ['id', 'desc'],
        ];
        $search = $input->getJson('search');
        if (!empty($search['status'])) {
            $option['search'][] = ['status' => ['equal' => intval($search['status'])]];
        }
        $paginateData = MemberMessageUtil::paginate(MemberUser::id(), $page, $pageSize, $option);
        return Response::generateSuccessPaginateData($page, $pageSize, $paginateData['records'], $paginateData['total']);
    }

    public function delete()
    {
        MemberMessageUtil::delete(MemberUser::id(), CRUDUtil::ids());
        return Response::generateSuccessData([
            'unreadMessageCount' => MemberMessageUtil::getUnreadMessageCount(MemberUser::id()),
        ]);
    }

    public function read()
    {
        MemberMessageUtil::updateRead(MemberUser::id(), CRUDUtil::ids());
        return Response::generateSuccessData([
            'unreadMessageCount' => MemberMessageUtil::getUnreadMessageCount(MemberUser::id()),
        ]);
    }

    public function readAll()
    {
        MemberMessageUtil::updateReadAll(MemberUser::id());
        return Response::generateSuccess();
    }

    public function deleteAll()
    {
        MemberMessageUtil::deleteAll(MemberUser::id());
        return Response::generateSuccess();
    }
}
