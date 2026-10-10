<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;
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
     *     "id": 1,
     *     "username": "demo",
     *     "nickname": "demo",
     *     "avatar": "",
     *     "email": "",
     *     "phone": "",
     *     "_certType": null
     *   }
     * }
     */
    public function current()
    {
        // [T-android-member-auth] 补充会员基础字段：此前本接口只回 _certType，
        // 移动端（BotX App / uniapp）登录后拿不到用户资料，只能再各显神通。
        // MemberUtil::get 直读 DB，比 Session 缓存的 _memberUser 更可靠。
        // 写法约束：项目运行环境 PHP 5.6/7.0，禁用 PHP 7.0+ 语法（见 AGENTS.md），
        // 空值守底用显式 empty() 判断，不用 ?? / ?:。
        $memberUser = MemberUtil::get(MemberUser::id());
        $data = array();
        if (!empty($memberUser)) {
            $data = ArrayUtil::keepKeys($memberUser, array('id', 'username', 'nickname', 'avatar', 'email', 'phone'));
        }
        $data['_certType'] = null;
        if (modstart_module_enabled('MemberCert')) {
            $data['_certType'] = MemberCertUtil::getCertType(MemberUser::id());
        }
        return Response::generateSuccessData($data);
    }
}
