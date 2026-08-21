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

/**
 * Class MemberAddressController
 * @package Module\Member\Api\Controller
 * @Api 用户信息
 */
class MemberAddressController extends ModuleBaseController implements MemberLoginCheck
{
    /**
     * @Api 收货地址列表
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前用户的收货地址列表
     * @ApiResponseData {
     *   "code": 0,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "姓名",
     *       "phone": "手机号",
     *       "area": "地区",
     *       "detail": "详细地址",
     *       "isDefault": true
     *     }
     *   ]
     * }
     */
    public function all()
    {
        return Response::generateSuccessData(MemberAddressUtil::listUserAddresses(MemberUser::id()));
    }

    /**
     * @Api 默认收货地址
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前用户的默认收货地址
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "id": 1,
     *     "name": "姓名",
     *     "phone": "手机号",
     *     "area": "地区",
     *     "detail": "详细地址",
     *     "isDefault": true
     *   }
     * }
     */
    public function getDefault()
    {
        return Response::generateSuccessData(MemberAddressUtil::getDefault(MemberUser::id()));
    }

    /**
     * @Api 收货地址保存
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 新增或编辑收货地址，id为空时新增
     * @ApiBodyParam id int 地址ID（编辑时必填）
     * @ApiBodyParam name string required 姓名
     * @ApiBodyParam phone string required 手机号
     * @ApiBodyParam area string required 地区
     * @ApiBodyParam detail string required 详细地址
     * @ApiBodyParam isDefault boolean 是否设为默认地址
     * @ApiResponseData {
     *   "code": 0
     * }
     */
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

    /**
     * @Api 收货地址删除
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 删除指定收货地址
     * @ApiBodyParam id int required 地址ID
     * @ApiResponseData {
     *   "code": 0
     * }
     */
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
