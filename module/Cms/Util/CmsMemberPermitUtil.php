<?php


namespace Module\Cms\Util;


use Module\Member\Auth\MemberUser;

class CmsMemberPermitUtil
{
    public static function canVisitCat($cat)
    {
        if ($cat['visitMemberGroupEnable']) {
            if (!MemberUser::isGroup($cat['visitMemberGroups'])) {
                return false;
            }
        }
        if ($cat['visitMemberVipEnable']) {
            if (!MemberUser::isVip($cat['visitMemberVips'])) {
                return false;
            }
        }
        return true;
    }

    public static function canPostCat($cat)
    {
        if (!$cat['memberUserPostEnable']) {
            return false;
        }
        if ($cat['postMemberGroupEnable']) {
            if (!MemberUser::isGroup($cat['postMemberGroups'])) {
                return false;
            }
        }
        if ($cat['postMemberVipEnable']) {
            if (!MemberUser::isVip($cat['postMemberVips'])) {
                return false;
            }
        }
        return true;
    }
}
