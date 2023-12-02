<?php


use Module\ContentBlock\Util\ContentBlockUtil;

/**
 * @Util 内容区块
 */
class MContentBlock
{
    /**
     * @Util 根据name获取内容区块，60分钟缓存
     * @param $name string name
     * @return array|null
     */
    public static function getCached($name)
    {
        return ContentBlockUtil::getCached($name);
    }

    /**
     * @Util 根据id获取内容区块，60分钟缓存
     * @param $id integer ID
     * @return array|null
     */
    public static function getByIdCached($id)
    {
        return ContentBlockUtil::getByIdCached($id);
    }

    /**
     * @Util 根据name获取内容区块列表，60分钟缓存
     * @param $name string 名称
     * @param $limit int 最多返回多少个
     * @return array
     */
    public static function allCached($name, $limit = 0)
    {
        return ContentBlockUtil::allCached($name, $limit);
    }


}
