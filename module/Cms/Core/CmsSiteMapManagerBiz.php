<?php


namespace Module\Cms\Core;


use Module\Cms\Util\CmsContentUtil;
use Module\SiteMapManager\Biz\AbstractSiteMapManagerBiz;

class CmsSiteMapManagerBiz extends AbstractSiteMapManagerBiz
{
    public function name()
    {
        return 'cms';
    }

    public function title()
    {
        return '通用CMS';
    }

    public function latestRecords()
    {
        $paginateData = CmsContentUtil::paginate(1, 50);
        return array_map(function ($record) {
            return [
                'loc' => $record['_url'],
                'updateTime' => $record['postTime'],
            ];
        }, $paginateData['records']);
    }

}
