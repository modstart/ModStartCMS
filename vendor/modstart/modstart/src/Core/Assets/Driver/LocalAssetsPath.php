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
        if (null !== $hash) {
            return $file . '?' . $hash;
        }
        if (file_exists($file)) {
            $hash = '' . crc32(md5_file($file));
            Cache::put($flag, $hash, 0);
            return $file . '?' . $hash;
        }
        Cache::put($flag, '', 0);
        return $file;
    }

    public function getCDN($file)
    {
        return config('modstart.asset.cdn');
    }
}
