<?php

use Module\Banner\Type\BannerType;
use Module\Banner\Util\BannerUtil;

/**
 * Class MBanner
 *
 * @Util 轮播
 */
class MBanner
{
    /**
     * @Util 根据位置获取轮播数据
     * @param $position string 位置
     * @return array
     */
    public static function all($position = 'home')
    {
        return BannerUtil::listByPositionWithCache($position);
    }

    /**
     * @Util 根据位置获取轮播数据（仅包含图片）
     * @param $position string 位置
     * @return array
     */
    public static function allImage($position)
    {
        $records = self::all($position);
        return array_values(array_filter($records, function ($item) {
            return in_array($item['type'], [
                BannerType::IMAGE,
                BannerType::IMAGE_TITLE_SLOGAN_LINK
            ]);
        }));
    }
}
