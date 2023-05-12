<?php


namespace Module\Vendor\Util;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\TreeUtil;

class FilterUtil
{
    private static function categoryGet($categories, $id)
    {
        foreach ($categories as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }
        return null;
    }

    public static function categoryTreeFilter($categoryId, $categories, $keyTitle = 'title', $keyPid = 'pid')
    {
        $filterText = [];
        $pageTitle = [];
        $category = null;
        if ($categoryId) {
            $category = self::categoryGet($categories, $categoryId);
            BizException::throwsIfEmpty('分类不存在', $category);
            $filterText[] = $category[$keyTitle];
            $pageTitle[] = $category[$keyTitle];
            if ($category[$keyPid]) {
                $parentCategory = self::categoryGet($categories, $category[$keyPid]);
                $pageTitle[] = $parentCategory[$keyTitle];
            }
        }
        $categoryIds = [
            $categoryId
        ];
        $childrenIds = TreeUtil::nodesChildrenIds($categories, $categoryId);
        $categoryIds = array_merge($categoryIds, $childrenIds);
        $categoryChain = TreeUtil::nodesChainWithItems($categories, $categoryId);
        return [
            $category,
            $categoryIds,
            $categoryChain,
            $filterText,
            $pageTitle,
        ];
    }
}
