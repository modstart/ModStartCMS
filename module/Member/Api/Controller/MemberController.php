<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\MemberCert\Util\MemberCertUtil;

class MemberController extends Controller implements MemberLoginCheck
{
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
