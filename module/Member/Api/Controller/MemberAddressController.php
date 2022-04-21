<?php


namespace Module\Member\Api\Controller;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberAddressUtil;

class MemberAddressController extends ModuleBaseController implements MemberLoginCheck
{
    public function all()
    {
        return Response::generateSuccessData(MemberAddressUtil::listUserAddresses(MemberUser::id()));
    }

    public function getDefault()
    {
        return Response::generateSuccessData(MemberAddressUtil::getDefault(MemberUser::id()));
    }

    public function edit()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $address = null;
        if ($id) {
            $address = MemberAddressUtil::getUserAddress(MemberUser::id(), $id);
            BizException::throwsIfEmpty('地址不存在', $address);
        }
        $data = [];
        $data['name'] = $input->getTrimString('name');
        $data['phone'] = $input->getTrimString('phone');
        $data['area'] = $input->getTrimString('area');
        $data['detail'] = $input->getTrimString('detail');
        $data['isDefault'] = $input->getBoolean('isDefault');
        BizException::throwsIfEmpty('姓名为空', $data['name']);
        BizException::throwsIfEmpty('手机为空', $data['phone']);
        BizException::throwsIfEmpty('地址为空', $data['area']);
        BizException::throwsIfEmpty('详细地址为空', $data['detail']);
        if ($data['isDefault']) {
            MemberAddressUtil::resetDefault(MemberUser::id());
        }
        if ($address) {
            MemberAddressUtil::update($address['id'], $data);
        } else {
            $data['memberUserId'] = MemberUser::id();
            MemberAddressUtil::insert($data);
        }
        return Response::generateSuccess();
    }

    public function delete()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $address = MemberAddressUtil::getUserAddress(MemberUser::id(), $id);
        BizException::throwsIfEmpty('地址不存在', $address);
        MemberAddressUtil::delete($address['id']);
        return Response::generateSuccess();
    }
}
