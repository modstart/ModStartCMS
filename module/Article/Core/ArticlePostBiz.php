<?php


namespace Module\Article\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
use Module\AiAutoArticle\Provider\AbstractAiAutoArticlePostBiz;

class ArticlePostBiz extends AbstractAiAutoArticlePostBiz
{
    const NAME = 'Article';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '通用文章';
    }

    public function push($article, $task, $param = [])
    {
        $bizParam = $this->bizParam($task);
        $data = [
            'position' => isset($bizParam['position']) ? $bizParam['position'] : '',
            'alias' => RandomUtil::lowerString(16),
            'title' => $article['title'],
            'content' => $article['content'],
        ];
        $result = ModelUtil::insert('article', $data);
        return ['id' => $result['id']];
    }
}
