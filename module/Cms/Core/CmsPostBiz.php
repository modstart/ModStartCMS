<?php


namespace Module\Cms\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
use Module\AiAutoArticle\Provider\AbstractAiAutoArticlePostBiz;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;

class CmsPostBiz extends AbstractAiAutoArticlePostBiz
{
    const NAME = 'Cms';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return 'CMS系统';
    }

    public function push($article, $task, $param = [])
    {
        $bizParam = $this->bizParam($task);
        $modelId = 0;
        if (!empty($bizParam['modelId'])) {
            $modelId = intval($bizParam['modelId']);
        }
        $catId = 0;
        if (!empty($bizParam['catId'])) {
            $catId = intval($bizParam['catId']);
        }
        $model = CmsModelUtil::get($modelId);
        $data = [
            'catId' => $catId,
            'title' => $article['title'],
            'alias' => RandomUtil::lowerString(16),
            'summary' => $article['description'],
            'cover' => $article['cover'],
            'postTime' => date('Y-m-d H:i:s'),
            'status' => CmsModelContentStatus::SHOW,
            'isRecommend' => true,
            'tags' => $article['keywords'],
            'seoTitle' => $article['title'],
            'seoKeywords' => $article['keywords'],
            'seoDescription' => $article['description'],
            'viewCount' => 0,
        ];
        $dataData = [
            'content' => $article['content'],
        ];
        $id = CmsContentUtil::insert($model, $data, $dataData);
        return ['id' => $id];
    }
}
