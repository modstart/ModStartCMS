<?php


namespace Module\Member\Auth;


use ModStart\Core\Util\ConvertUtil;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberVipUtil;

class MemberGroup
{
    public static function get($key = null)
    {
        static $memberGroup = null;
        if (null === $memberGroup) {
            $memberGroup = MemberGroupUtil::getMemberGroup(MemberUser::user());
        }
        if (null !== $key) {
            return $memberGroup[$key];
        }
        return $memberGroup;
    }

    public static function inGroupIds($groupIds)
    {
        if (!is_array($groupIds)) {
            $groupIds = ConvertUtil::toArray($groupIds);
        }
        $id = self::get('id');
        // var_dump($id);var_dump($groupIds);exit();
        if (empty($id)) {
            return false;
        }
        return in_array($id, $groupIds);
    }
}
