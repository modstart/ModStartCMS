<?php

namespace Module\Vendor\Util;

use Illuminate\Support\Str;
use ModStart\Core\Util\LogUtil;

class OptimizeUtil
{
    public static function url($url)
    {
        $serverType = config('env.SERVER_TYPE');
        if (empty($serverType)) {
            return $url;
        }
        // replace oss-cn-<location>.aliyuncs.com to oss-cn-<location>-internal.aliyuncs.com
        if ($serverType == 'aliyun') {
            $location = config('env.SERVER_ALIYUN_LOCATION');
            if ($location) {
                if (Str::contains($url, "oss-cn-{$location}.aliyuncs.com")) {
                    $url = str_replace(
                        "oss-cn-{$location}.aliyuncs.com",
                        "oss-cn-{$location}-internal.aliyuncs.com",
                        $url
                    );
                }
                return $url;
            }
            LogUtil::info('OptimizeUtil', 'SERVER_ALIYUN_LOCATION is empty, cannot optimize aliyun oss url');
        }
        LogUtil::info('OptimizeUtil', "SERVER_TYPE is {$serverType}, cannot optimize url");
        return $url;
    }
}
