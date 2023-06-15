<?php

namespace ModStart\Core\Util;

class EnvUtil
{
    public static function env($key)
    {
        switch ($key) {
            case 'uploadMaxSize':
                // 模拟分片上传时，每个分片的大小
                // return 100;
                $upload_max_filesize = @ini_get('upload_max_filesize');
                if (empty($upload_max_filesize)) {
                    return 0;
                }
                $post_max_size = @ini_get('post_max_size');
                if (empty($post_max_size)) {
                    return 0;
                }
                $upload_max_filesize = FileUtil::formattedSizeToBytes($upload_max_filesize);
                $post_max_size = FileUtil::formattedSizeToBytes($post_max_size);
                $size = min($upload_max_filesize, $post_max_size);
                // 文件上传时附加信息会占用部分
                $size -= 10 * 1024;
                // 最少100KB，最大2M
                return min(max($size, 100 * 1024), 2 * 1024 * 1024);
        }
        return null;
    }

    public static function iniFileConfig($key)
    {
        return @ini_get($key);
    }
}
