<?php


namespace Module\TagManager\Model;


use Illuminate\Database\Eloquent\Model;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\TagUtil;

/**
 * Class TagManager
 * @package Module\TagManager\Model
 * @Util 标签云管理
 */
class TagManager extends Model
{
    /**
     * @Util 查找所有可见标签
     * @param $biz string 业务标识
     * @return array 标签记录
     */
    public static function allVisible($biz)
    {
        return ModelUtil::all('tag_manager', [
            'biz' => $biz,
            'isShow' => true,
        ]);
    }

    /**
     * @Util 查找所有标签
     * @param $biz string 业务标识
     * @return array 标签数组
     */
    public static function allTagsValue($biz)
    {
        return ModelUtil::values('tag_manager', 'tag', ['biz' => $biz]);
    }

    public static function prepareTags($biz, $tags)
    {
        self::normalTags($tags);
    }

    /**
     * @Util 增加标签
     * @param $biz string 业务标识
     * @param $tags array 标签
     */
    public static function putTags($biz, $tags)
    {
        if (empty($tags)) {
            return;
        }
        foreach (self::normalTags($tags) as $tag) {
            self::putTagSafely($biz, $tag);
        }
    }

    /**
     * @Util 修改标签
     * @param $biz string 业务标识
     * @param $oldTags array 旧标签
     * @param $newTags array 新标签
     */
    public static function updateTags($biz, $oldTags, $newTags)
    {
        if ($oldTags == $newTags) {
            return;
        }
        $oldTags = self::normalTags($oldTags);
        $newTags = self::normalTags($newTags);
        $deletes = [];
        $inserts = [];
        foreach ($newTags as $tag) {
            if (!in_array($tag, $oldTags)) {
                $inserts[] = $tag;
            }
        }
        foreach ($oldTags as $tag) {
            if (!in_array($tag, $newTags)) {
                $deletes[] = $tag;
            }
        }
        foreach ($inserts as $tag) {
            self::putTagSafely($biz, $tag);
        }
        foreach ($deletes as $tag) {
            self::deleteTagSafely($biz, $tag);
        }
    }

    /**
     * @Util 删除标签
     * @param $biz string 业务标识
     * @param $tags array 标签
     */
    public static function deleteTags($biz, $tags)
    {
        foreach (self::normalTags($tags) as $tag) {
            self::deleteTagSafely($biz, $tag);
        }
    }

    private static function normalTags($tags)
    {
        if (is_string($tags)) {
            if (str_contains($tags, ':')) {
                $tags = TagUtil::string2Array($tags);
            } else {
                $tags = [$tags];
            }
        }
        foreach ($tags as $tag) {
            BizException::throwsIf('标签' . $tag . '太长', strlen($tag) > 50);
        }
        return $tags;
    }

    private static function deleteTagSafely($biz, $tag)
    {
        $where = [
            'biz' => $biz,
            'tag' => $tag,
        ];
        if (ModelUtil::increase('tag_manager', $where, 'cnt', -1) > 0) {
            $o = ModelUtil::get('tag_manager', $where);
            if ($o['cnt'] <= 0) {
                ModelUtil::update('tag_manager', $where, [
                    'cnt' => 0,
                ]);
            }
        }
    }

    private static function putTagSafely($biz, $tag)
    {
        $where = [
            'biz' => $biz,
            'tag' => $tag,
        ];
        if (ModelUtil::increase('tag_manager', $where, 'cnt') <= 0) {
            ModelUtil::insert('tag_manager', array_merge($where, [
                'cnt' => 1,
                'isShow' => false,
            ]));
        }
    }

}
