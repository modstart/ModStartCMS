<?php


/**
 * Class MPartner
 *
 * @Util 友情链接
 */
class MPartner
{
    /**
     * @param string $position
     * @return mixed
     *
     * @Util 根据位置获取友情链接数据
     */
    public static function all($position = 'home')
    {
        return \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
    }
}