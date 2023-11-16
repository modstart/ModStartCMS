<?php


namespace Module\Cms\Core;


use ModStart\Core\Dao\ModelUtil;
use Module\Cms\Util\UrlUtil;
use Module\Vendor\Provider\SiteUrl\AbstractSiteUrlBiz;

class CmsSiteUrlBiz extends AbstractSiteUrlBiz
{
    const NAME = 'cms';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return 'CMS';
    }

    public function urlBuildBatch($nextId, $param = [])
    {
        $records = [];
        $batchRet = ModelUtil::batch('cms_content', $nextId);
        $finish = empty($batchRet['records']);
        foreach ($batchRet['records'] as $record) {
            $records[] = [
                'url' => UrlUtil::content($record),
                'updateTime' => $record['updated_at'],
            ];
        }
        return [
            'finish' => $finish,
            'records' => $records,
            'nextId' => $batchRet['nextId'],
        ];
    }

}
