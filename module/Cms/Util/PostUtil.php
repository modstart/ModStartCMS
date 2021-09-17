<?php

namespace Module\Cms\Util;

use Illuminate\Support\Str;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\TreeUtil;

class PostUtil
{
    public static function latestPostsByChannel($channelId, $limit = 3)
    {
        $nodes = ChannelUtil::all();
        $channelIds = TreeUtil::nodesChildrenIds($nodes, $channelId);
        $channelIds = array_merge([$channelId], $channelIds);
        $option['order'] = ['id', 'desc'];
        $option['whereIn'] = ['channelId', $channelIds];
        $paginateData = self::paginatePosts(1, $limit, $option);
        return $paginateData['records'];
    }

    public static function latestPosts($limit)
    {
        $option['order'] = ['id', 'desc'];
        $paginateData = self::paginatePosts(1, $limit, $option);
        return $paginateData['records'];
    }

    public static function paginatePosts($page, $pageSize, $option = [])
    {
        $option['where']['isDeleted'] = false;
        $paginateData = ModelUtil::paginate('cms_post', $page, $pageSize, $option);
        $channelMap = ChannelUtil::mapById();
        foreach ($paginateData['records'] as &$record) {
            $d = HtmlUtil::extractTextAndImages($record['contentHtml']);
            $record['_summary'] = Str::limit($d['text'], 200);
            $record['_cover'] = (empty($d['images'][0]) ? null : $d['images'][0]);
            $record['_channel'] = isset($channelMap[$record['channelId']]) ? $channelMap[$record['channelId']] : null;
        }
        return $paginateData;
    }


    
    public static function getByAlias($alias)
    {
        return ModelUtil::get('cms_post', ['alias' => $alias, 'isDeleted' => false,]);
    }

    public static function get($id)
    {
        return ModelUtil::get('cms_post', ['id' => $id, 'isDeleted' => false,]);
    }

    public static function update($postId, $data)
    {
        return ModelUtil::update('cms_post', ['id' => $postId], $data);
    }

    public static function paginateMemberUserPosts($memberUserId, $page, $pageSize, $option = [])
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['where']['isDeleted'] = false;
        $paginateData = ModelUtil::paginate('cms_post', $page, $pageSize, $option);
        foreach ($paginateData['records'] as &$record) {
            $d = HtmlUtil::extractTextAndImages($record['contentHtml']);
            $record['_summary'] = Str::limit($d['text'], 200);
            $record['_cover'] = (empty($d['images'][0]) ? null : $d['images'][0]);
        }
        return $paginateData;
    }

    public static function isLiked($memberUserId, $postId)
    {
        $data = [
            'postId' => $postId,
            'memberUserId' => $memberUserId,
        ];
        if (ModelUtil::exists('cms_post_like', $data)) {
            return true;
        }
        return false;
    }




    public static function updatePostCommentStat($postId)
    {
        $commentCount = ModelUtil::count('cms_post_comment', ['postId' => $postId]);
        ModelUtil::update('cms_post', ['id' => $postId], ['commentCount' => $commentCount]);
    }

    public static function delete($id)
    {
        ModelUtil::delete('cms_post', ['id' => $id]);
        ModelUtil::delete('cms_post_comment', ['postId' => $id]);
        ModelUtil::delete('cms_post_like', ['postId' => $id]);
    }

    public static function deleteByMemberPostId($memberPostId)
    {
        $m = ModelUtil::get('cms_post', ['memberPostId' => $memberPostId]);
        if (!empty($m)) {
            self::delete($m['id']);
        }
    }

}
