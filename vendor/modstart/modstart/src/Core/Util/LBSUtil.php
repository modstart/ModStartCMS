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

    public static function locationByIP($ip, $type = null)
    {
        if (null === $type) {
            $order = [1, 2, 3];
        } else {
            $order = [$type];
        }
        shuffle($order);
        $value = null;
        foreach ($order as $type) {
            $value = self::locationByIPRandom($type, $ip);
            if ($value) {
                return $value;
            }
        }
        return null;
    }

    private static function locationByIPRandom($type, $ip)
    {
        switch ($type) {
            case 1:
                $cityQuery = CurlUtil::getJSONData('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . urlencode($ip));
                if (!empty($cityQuery)) {
                    $country = empty($cityQuery['country']) ? '' : $cityQuery['country'];
                    $province = empty($cityQuery['province']) ? '' : $cityQuery['province'];
                    $city = empty($cityQuery['city']) ? '' : $cityQuery['city'];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
            case 2:
                $cityQuery = CurlUtil::getJSONData("http://ip.taobao.com/service/getIpInfo.php?ip=" . urlencode($ip));
                if (isset($cityQuery['code']) && $cityQuery['code'] == 0) {
                    $country = empty($cityQuery['data']['country']) ? '' : $cityQuery['data']['country'];
                    $province = empty($cityQuery['data']['region']) ? '' : $cityQuery['data']['region'];
                    $city = empty($cityQuery['data']['city']) ? '' : $cityQuery['data']['city'];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
            case 3:
                $cityQuery = CurlUtil::getJSONData("http://freeapi.ipip.net/" . urlencode($ip));
                if (isset($cityQuery[0])) {
                    $country = empty($cityQuery[0]) ? '' : $cityQuery[0];
                    $province = empty($cityQuery[1]) ? '' : $cityQuery[1];
                    $city = empty($cityQuery[2]) ? '' : $cityQuery[2];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
        }
        return null;
    }
}
