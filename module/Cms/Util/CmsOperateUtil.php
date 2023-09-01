<?php


namespace Module\Cms\Util;


use Illuminate\Support\Facades\Session;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;

class CmsOperateUtil
{
    public static function like($id)
    {
        $key = 'Cms_LikeIds';
        $ids = Session::get($key, []);
        if (empty($ids)) {
            $ids = [];
        }
        $record = ModelUtil::get('cms_content', $id);
        if (empty($record)) {
            return Response::generateError('内容不存在');
        }
        $action = 'unliked';
        $count = intval($record['likeCount']);
        if (!in_array($id, $ids)) {
            $ids[] = $id;
            $action = 'liked';
            $count++;
            ModelUtil::increase('cms_content', $id, 'likeCount', 1);
        } else {
            $ids = array_diff($ids, [$id]);
            $count--;
            ModelUtil::increase('cms_content', $id, 'likeCount', -1);
        }
        Session::put($key, $ids);
        return Response::generateSuccessData([
            'action' => $action,
            'count' => $count,
        ]);
    }

    public static function isLiked($id)
    {
        $key = 'Cms_LikeIds';
        $ids = Session::get($key, []);
        if (empty($ids)) {
            $ids = [];
        }
        return in_array($id, $ids);
    }
}
