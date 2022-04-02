<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;

/**
 * Class MemberFavoriteUtil
 * @package Module\Member\Util
 * @deprecated
 */
class MemberFavoriteUtil
{
    public static function urlFavorite($category, $categoryId, $redirect = '')
    {
        return modstart_api_url('member_favorite/favorite', ['category' => $category, 'categoryId' => $categoryId, 'redirect' => $redirect]);
    }

    public static function urlUnfavorite($category, $categoryId, $redirect = '')
    {
        return modstart_api_url('member_favorite/unfavorite', ['category' => $category, 'categoryId' => $categoryId, 'redirect' => $redirect]);
    }

    public static function add($userId, $category, $categoryId)
    {
        $m = ModelUtil::get('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
        if (empty($m)) {
            ModelUtil::insert('member_favorite', [
                'userId' => $userId, 'category' => $category, 'categoryId' => $categoryId
            ]);
        }
    }

    public static function delete($userId, $category, $categoryId)
    {
        ModelUtil::delete('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
    }

    public static function clean($category, $categoryId)
    {
        ModelUtil::delete('member_favorite', ['category' => $category, 'categoryId' => $categoryId]);
    }

    public static function exists($userId, $category, $categoryId)
    {
        return ModelUtil::exists('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
    }

    public static function paginate($userId, $category, $page, $pageSize, $option = [])
    {
        $option['where']['userId'] = $userId;
        $option['where']['category'] = $category;
        return ModelUtil::paginate('member_favorite', $page, $pageSize, $option);
    }

}
