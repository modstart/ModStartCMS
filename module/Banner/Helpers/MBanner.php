<?php

use Module\Banner\Util\BannerUtil;

/**
 * Class MBanner
 *
 * @Util 通用轮播
 */
class MBanner
{
    /**
     * @param string $position
     * @return mixed
     *
     * @Util 根据位置获取轮播数据
     */
    public static function all($position = 'home')
    {
        return BannerUtil::listByPositionWithCache($position);
    }
}