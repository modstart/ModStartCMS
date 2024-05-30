<?php


namespace Module\Member\Util;


use ModStart\Support\Manager\FieldManager;
use Module\Member\Widget\Field\AdminMemberInfo;
use Module\Member\Widget\Field\AdminMemberSelector;
use Module\Member\Widget\Field\MemberImage;

class MemberFieldUtil
{
    public static function register()
    {
        FieldManager::extend('memberImage', MemberImage::class);
        FieldManager::extend('adminMemberInfo', AdminMemberInfo::class);
        FieldManager::extend('adminMemberSelector', AdminMemberSelector::class);
    }
}
