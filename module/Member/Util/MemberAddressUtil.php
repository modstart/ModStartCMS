<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;

class MemberAddressUtil
{
    public static function getUserAddress($memberUserId, $id)
    {
        return ModelUtil::get('member_address', ['id' => $id, 'memberUserId' => $memberUserId]);
    }

    public static function listUserAddresses($memberUserId)
    {
        return ModelUtil::model('member_address')->where(['memberUserId' => $memberUserId])->orderBy('id', 'desc')->orderBy('isDefault', 'desc')->get()->toArray();
    }

    public static function delete($id)
    {
        ModelUtil::delete('member_address', ['id' => $id]);
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('member_address', ['id' => $id], $data);
    }

    public static function insert($data)
    {
        return ModelUtil::insert('member_address', $data);
    }

    public static function getDefault($memberUserId)
    {
        $address = ModelUtil::get('member_address', ['memberUserId' => $memberUserId, 'isDefault' => true]);
        if (empty($address)) {
            $address = ModelUtil::get('member_address', ['memberUserId' => $memberUserId]);
        }
        return $address;
    }

    public static function clearDefault($memberUserId)
    {
        ModelUtil::update('member_address', ['memberUserId' => $memberUserId], ['isDefault' => false]);
    }

    public static function truncate($memberUserId)
    {
        ModelUtil::delete('member_address', ['memberUserId' => $memberUserId]);
    }

}
