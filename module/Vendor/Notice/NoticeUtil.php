<?php

namespace Module\Vendor\Notice;

use ModStart\Core\Util\CurlUtil;
use Module\I18n\Util\ConfigUtil;

class NoticeUtil
{
    public static function sendConfigNotice($key, $msg = [])
    {
        $url = modstart_config($key);
        if (empty($url)) {
            return;
        }
        try {
            CurlUtil::get($url, ['data' => json_encode($msg)]);
        } catch (\Exception $e) {
        }
    }
}