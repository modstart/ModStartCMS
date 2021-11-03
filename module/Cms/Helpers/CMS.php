<?php

class Cms
{
    public static function paginateCatByUrl($catUrl, $page, $pageSize, $option = [])
    {
        $cat = \Module\Cms\Util\CmsCatUtil::getByUrl($catUrl);
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
        return $paginateData['records'];
    }

    public static function paginateCat($catId, $page, $pageSize, $option = [])
    {
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }
}
