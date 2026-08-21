<?php


namespace Module\Member\Api\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberCreditUtil;

/**
 * Class MemberCreditController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberCreditController extends ModuleBaseController implements MemberLoginCheck
{
    /**
     * @Api 获取用户积分
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前用户的积分余额
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "total": 100,
     *     "freezeTotal": 0
     *   }
     * }
     */
    public function get()
    {
        $credit = MemberCreditUtil::get(MemberUser::id());
        return Response::generateSuccessData([
            'total' => $credit ? $credit['total'] : 0,
            'freezeTotal' => $credit ? $credit['freezeTotal'] : 0,
        ]);
    }


    /**
     * @Api 用户积分明细
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 分页获取当前用户的积分变动记录
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
     *         "change": 10
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
