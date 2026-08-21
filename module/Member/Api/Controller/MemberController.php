<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\MemberCert\Util\MemberCertUtil;

/**
 * Class MemberController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberController extends Controller implements MemberLoginCheck
{
    /**
     * @Api 当前用户信息
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前登录用户的基础信息
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "_certType": null
     *   }
     * }
     */
    public function current()
    {
        $data = [];
        $data['_certType'] = null;
        if (modstart_module_enabled('MemberCert')) {
            $data['_certType'] = MemberCertUtil::getCertType(MemberUser::id());
        }
        return Response::generateSuccessData($data);
    }
}
