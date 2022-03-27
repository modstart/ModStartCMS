<?php


namespace Module\Article\Biz;


use Module\Vendor\Biz\BizTrait;

class ArticlePositionBiz
{
    use BizTrait;

    /**
     * @return AbstractArticlePositionBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractArticlePositionBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
