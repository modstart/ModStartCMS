<?php

namespace Module\Vendor\Util;

use ModStart\Core\Util\EncodeUtil;

class EntryProcessorUtil
{
    public static function buildRedirectEntry($page, $param = [])
    {
        return self::package([
            'type' => 'redirect',
            'data' => [
                'page' => $page,
                'param' => empty($param) ? new \stdClass() : $param,
            ]
        ]);
    }

    public static function buildBizEntry($name, $param = [])
    {
        return self::package([
            'type' => 'biz',
            'data' => [
                'name' => $name,
                'param' => empty($param) ? new \stdClass() : $param,
            ]
        ]);
    }

    private static function package($data)
    {
        return EncodeUtil::base64UrlSafeEncode(json_encode($data));
    }

}
