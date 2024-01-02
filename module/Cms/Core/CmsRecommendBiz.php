<?php


namespace Module\Cms\Core;


use Module\Cms\Model\CmsContent;
use Module\Vendor\Provider\Recommend\AbstractRecommendBiz;

class CmsRecommendBiz extends AbstractRecommendBiz
{
    const NAME = 'Cms';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return 'CMS';
    }

    public function providerName()
    {
        return modstart_config('Cms_RecommendProvider', '');
    }

    public function syncBatch($nextId, $param = [])
    {
        $recordList = CmsContent::with(['cat'])
            ->where('id', '>', $nextId)
            ->orderBy('id', 'asc')
            ->limit(100)
            ->get()->toArray();
        $records = [];
        foreach ($recordList as $record) {
            $tags = [];
            if ($record['cat']) {
                $tags[] = $record['cat']['title'];
            }
            $records[] = [
                'biz' => self::NAME,
                'bizId' => $record['id'],
                'sceneId' => $record['modelId'],
                'tags' => $tags,
            ];
            $nextId = $record['id'];
        }
        return [
            'records' => $records,
            'nextId' => $nextId,
        ];
    }


}
