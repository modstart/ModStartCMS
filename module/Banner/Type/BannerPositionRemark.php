<?php


namespace Module\Banner\Type;


use Module\Banner\Biz\BannerPositionBiz;

class BannerPositionRemark extends BannerPosition
{
    public static function getList()
    {
        $map = parent::getList();
        foreach ($map as $k => $v) {
            $biz = BannerPositionBiz::get($k);
            if ($biz && $biz->remark()) {
                $map[$k] = $v . '(' . $biz->remark() . ')';
            }
        }
        return $map;
    }
}
