<?php


namespace Module\TagManager\Biz;


use Module\TagManager\Model\TagManager;

abstract class AbstractTagManagerBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function searchUrl($tag);

    public function syncBatch($nextId)
    {
        $data = [];
        $data['nextId'] = $nextId;
        $data['tags'] = [];
        $data['finish'] = true;
        return $data;
    }

    public static function prepareTags($tags)
    {
        TagManager::prepareTags(static::NAME, $tags);
    }

    public static function putTags($tags)
    {
        TagManager::putTags(static::NAME, $tags);
    }

    public static function updateTags($oldTags, $newTags)
    {
        TagManager::updateTags(static::NAME, $oldTags, $newTags);
    }

    public static function deleteTags($tags)
    {
        TagManager::deleteTags(static::NAME, $tags);
    }
}
