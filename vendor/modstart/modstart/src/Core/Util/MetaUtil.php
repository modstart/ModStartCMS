<?php


namespace ModStart\Core\Util;


use ModStart\Core\Exception\BizException;

class MetaUtil
{
    private static $supportKeys = [
        'APP',
        'APP_NAME',
        'VERSION',
    ];

    public static function get($key)
    {
        static $meta = null;
        BizException::throwsIf('Meta信息不包含' . $key, !in_array($key, self::$supportKeys));
        if (null === $meta) {
            $file = base_path('meta.json');
            if (file_exists($file)) {
                $meta = @json_decode(file_get_contents($file), true);
            }
            if (empty($meta)) {
                $meta = [];
            }
        }
        if (isset($meta[$key])) {
            return $meta[$key];
        }
        switch ($key) {
            case 'APP_NAME':
                if (defined('\App\Constant\AppConstant::APP_NAME')) {
                    return \App\Constant\AppConstant::APP_NAME;
                } else if (defined('\App\Constant\AppConstant::APP')) {
                    return \App\Constant\AppConstant::APP;
                }
                return 'Unknown';
            case 'APP':
                if (defined('\App\Constant\AppConstant::APP')) {
                    return \App\Constant\AppConstant::APP;
                }
                return 'Unknown';
            case 'VERSION':
                if (defined('\App\Constant\AppConstant::VERSION')) {
                    return \App\Constant\AppConstant::VERSION;
                }
                return '0.0.0';
        }
        return null;
    }
}
