<?php

namespace Module\CmsWriter\Util;


use ModStart\Core\Dao\ModelUtil;

class WriterUtil
{
    public static function categoryAll($memberUserId)
    {
        return ModelUtil::all('cms_member_post_category',
            ['memberUserId' => $memberUserId],
            ['id', 'title'],
            ['id', 'desc']
        );
    }
}
