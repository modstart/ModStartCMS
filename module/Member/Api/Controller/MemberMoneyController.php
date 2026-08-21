<?php


namespace Module\Member\Api\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberMoneyUtil;

/**
 * Class MemberMoneyController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberMoneyController extends ModuleBaseController implements MemberLoginCheck
{
    /**
     * @Api 获取用户资金
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前用户的资金余额
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "total": "100.00"
     *   }
     * }
     */
    public function get()
    {
        return Response::generateSuccessData([
            'total' => MemberMoneyUtil::getTotal(MemberUser::id())
        ]);
    }

    /**
     * @Api 获取用户资金明细
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 分页获取当前用户的资金变动记录
     * @ApiBodyParam search.type string 类型：income收入|payout支出
     * @ApiBodyParam page int 页码
     * @ApiBodyParam pageSize int 每页数量
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "page": 1,
     *     "pageSize": 10,
     *     "records": [
     *       {
     *         "id": 1,
     *         "change": "10.00"
     *       }
     *     ],
     *     "total": 1,
     *     "maxRecords": -1
     *   }
     * }
     */
    public function log()
    {
        $input = InputPackage::buildFromInput();
        $option = [];
        $searchInput = $input->getSearchInput();
        $type = $searchInput->getTrimString('type');
        switch ($type) {
            case 'income':
                $option['whereOperate'] = ['change', '>', '0'];
                break;
            case 'payout':
                $option['whereOperate'] = ['change', '<', '0'];
                break;
        }
        $paginateData = MemberMoneyUtil::paginateLog(
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
