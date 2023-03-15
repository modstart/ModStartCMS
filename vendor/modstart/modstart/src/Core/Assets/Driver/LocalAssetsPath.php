<?php

namespace ModStart\Core\Assets\Driver;

use ModStart\Core\Assets\AssetsPath;
use Illuminate\Support\Facades\Cache;

class LocalAssetsPath implements AssetsPath
{
    const CACHE_PREFIX = 'modstart:asset-file:';

    public function getPathWithHash($file)
    {
        $hash = Cache::get($flag = self::CACHE_PREFIX . $file, null);
        $joiner = (false === strpos($file, '?')) ? '?' : '&';
        if (null !== $hash) {
            return $file . $joiner . $hash;
        }
        $localFile = $file;
        if ('&' == $joiner) {
            $localFile = substr($file, 0, strpos($file, '?'));
        }
        if (file_exists($localFile)) {
            $hash = '' . crc32(md5_file($localFile));
            Cache::put($flag, $hash, 0);
            return $file . $joiner . $hash;
        }
        Cache::put($flag, '', 0);
        return $file;
    }

    public function getCDN($file)
    {
        return config('modstart.asset.cdn');
    }
}
