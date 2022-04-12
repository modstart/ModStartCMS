<?php

namespace Module\CmsUrlMix\Util;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;

class CmsUrlMixUtil
{
    public static function get($id)
    {
        return ModelUtil::get('cms_url_mix', $id);
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('cms_url_mix', $id, $data);
    }

    public static function all()
    {
        return ModelUtil::all('cms_url_mix', [], ['*'], ['sort', 'asc']);
    }
}
