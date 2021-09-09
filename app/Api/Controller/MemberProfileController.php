<?php


namespace App\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;

class MemberProfileController extends Controller implements MemberLoginCheck
{
    public function basic($data = null)
    {
        $input = InputPackage::buildFromInput();
        if (null === $data) {
            $data = $input->all();
        }
        MemberUtil::update(MemberUser::id(), ArrayUtil::keepKeys($data, ['gender', 'realname', 'signature']));
        return Response::jsonSuccess('保存成功');
    }
}