<?php


namespace Module\Cms\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TagUtil;
use Module\TagManager\Biz\AbstractTagManagerBiz;

class CmsTagManagerBiz extends AbstractTagManagerBiz
{
    public function name()
    {
        return 'cms';
    }

    public function title()
    {
        return '通用CMS';
    }

    public function searchUrl($tag)
    {
        return modstart_web_url('tag/' . urlencode($tag));
    }

    public function syncBatch($nextId)
    {
        $batch = ModelUtil::batch('cms_content', $nextId, 100);
        TagUtil::recordsString2Array($batch['records'], ['tags']);
        $tags = [];
        foreach ($batch['records'] as $record) {
            $tags = array_merge($tags, $record['tags']);
        }
        $data = [];
        $data['nextId'] = $batch['nextId'];
        $data['tags'] = $tags;
        $data['finish'] = empty($batch['records']);
        return $data;
    }


}
