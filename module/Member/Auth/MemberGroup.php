<?php


namespace Module\Member\Auth;


use ModStart\Core\Util\ConvertUtil;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberVipUtil;

class MemberGroup
{
    public static function get($key = null, $defaultValue = null)
    {
        static $memberGroup = null;
        if (null === $memberGroup) {
            $memberGroup = MemberGroupUtil::getMemberGroup(MemberUser::user());
        }
        if (null !== $key) {
            return $memberGroup ? $memberGroup[$key] : $defaultValue;
        }
        return $memberGroup;
    }

    public static function isDefault()
    {
        return self::get('isDefault', false);
    }

    public static function id()
    {
        return self::get('id', 0);
    }

    /**
     * @deprecated delete at 2023-10-18
     */
    public static function inGroupIds($groupIds)
    {
        return self::is($groupIds);
    }

    public static function is($groupIds)
    {
        if (!is_array($groupIds)) {
            $groupIds = ConvertUtil::toArray($groupIds);
        }
        $id = self::get('id');
        if (empty($id)) {
            return false;
        }
        return in_array($id, $groupIds);
    }
}
