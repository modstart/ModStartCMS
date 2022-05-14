<?php

namespace ModStart\Core\Util;


class LBSUtil
{
    public static function distance($fP1Lng, $fP1Lat, $fP2Lng, $fP2Lat)
    {
        $fEARTH_RADIUS = 6378137;
        //角度换算成弧度
        $fRadLon1 = deg2rad($fP1Lng);
        $fRadLon2 = deg2rad($fP2Lng);
        $fRadLat1 = deg2rad($fP1Lat);
        $fRadLat2 = deg2rad($fP2Lat);
        //return $fP1Lon - $fP2Lon;
        //计算经纬度的差值
        $fD1 = abs($fRadLat1 - $fRadLat2);
        $fD2 = abs($fRadLon1 - $fRadLon2);
        //return $fD2;
        //距离计算
        $fP = pow(sin($fD1 / 2), 2) +
            cos($fRadLat1) * cos($fRadLat2) * pow(sin($fD2 / 2), 2);
        return intval($fEARTH_RADIUS * 2 * asin(sqrt($fP)) + 0.5);

    }

    public static function formatDistance($distance)
    {
        if ($distance > 1000) {
            return round($distance / 1000.0, 1) . 'KM';
        } else {
            return intval($distance) . 'M';
        }
    }

    /**
     * @param $ip
     * @param null $type
     * @return array|string[]|null
     * @deprecated
     */
    public static function locationByIP($ip, $type = null)
    {
        return null;
    }
}
