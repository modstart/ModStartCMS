<?php


namespace Module\Member\Api\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberMessageUtil;

/**
 * Class MemberMessageController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberMessageController extends ModuleBaseController implements MemberLoginCheck
{
    /**
     * @Api 用户消息分页
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 分页获取当前用户的消息列表
     * @ApiBodyParam page int 页码
     * @ApiBodyParam search.status int 消息状态
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "page": 1,
     *     "pageSize": 10,
     *     "records": [
     *       {
     *         "id": 1,
     *         "title": "消息标题",
     *         "status": 0
     *       }
     *     ],
     *     "total": 1,
     *     "maxRecords": -1
     *   }
     * }
     */
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

    /**
     * @Api 用户消息删除
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 删除指定用户消息
     * @ApiBodyParam ids array required 消息ID列表
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "unreadMessageCount": 0
     *   }
     * }
     */
    public function delete()
    {
        MemberMessageUtil::delete(MemberUser::id(), CRUDUtil::ids());
        return Response::generateSuccessData([
            'unreadMessageCount' => MemberMessageUtil::getUnreadMessageCount(MemberUser::id()),
        ]);
    }

    /**
     * @Api 用户消息标记已读
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 将指定用户消息标记为已读
     * @ApiBodyParam ids array required 消息ID列表
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "unreadMessageCount": 0
     *   }
     * }
     */
    public function read()
    {
        MemberMessageUtil::updateRead(MemberUser::id(), CRUDUtil::ids());
        return Response::generateSuccessData([
            'unreadMessageCount' => MemberMessageUtil::getUnreadMessageCount(MemberUser::id()),
        ]);
    }

    /**
     * @Api 用户消息全部已读
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 将当前用户的所有消息标记为已读
     * @ApiResponseData {
     *   "code": 0
     * }
     */
    public function readAll()
    {
        MemberMessageUtil::updateReadAll(MemberUser::id());
        return Response::generateSuccess();
    }

    /**
     * @Api 用户消息全部删除
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 删除当前用户的所有消息
     * @ApiResponseData {
     *   "code": 0
     * }
     */
    public function deleteAll()
    {
        MemberMessageUtil::deleteAll(MemberUser::id());
        return Response::generateSuccess();
    }
}
