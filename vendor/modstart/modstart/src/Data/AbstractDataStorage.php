<?php

namespace ModStart\Data;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemInterface;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\EnvUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\PathUtil;
use ModStart\Data\Repository\DatabaseDataRepository;

abstract class AbstractDataStorage
{
    const DATA_TEMP = 'data_temp';
    const DATA = 'data';
    const DATA_CHUNK = 'data_chunk';

    const PATTERN_DATA_TEMP = '/^data_temp\\/([a-z_]+)\\/([a-zA-Z0-9]{32}\\.[a-z0-9]*)$/';
    const PATTERN_DATA = '/^data\\/([a-z_]+)\\/(\\d+\\/\\d+\\/\\d+\\/\\d+_[a-zA-Z0-9]{4}_\\d+\\.[a-z0-9]+)$/';
    const PATTERN_DATA_STRING = '/data\\/([a-z_]+)\\/(\\d+\\/\\d+\\/\\d+\\/\\d+_[a-zA-Z0-9]{4}_\\d+\\.[a-z0-9]+)/';

    /** @var FilesystemInterface */
    protected $localStorage;
    /** @var AbstractDataRepository */
    protected $repository;
    protected $option = [];

    /**
     * AbstractStorageService constructor.
     * @param array $option
     */
    public function __construct($option)
    {
        $this->option = $option;
        config(['filesystems.disks.data' => [
            'driver' => 'local',
            'root' => base_path('public/')
        ]]);
        $this->localStorage = Storage::disk('data');
        $this->repository = new DatabaseDataRepository();
    }

    public function driverName()
    {
        return null;
    }

    abstract public function init();

    abstract public function has($file);

    abstract public function move($from, $to);

    abstract public function delete($file);

    public function softDelete($file)
    {
        return $this->move($file, self::DATA . '/_trash/' . date('Ymd_H') . '/' . $file);
    }

    abstract public function put($file, $content);

    abstract public function get($file);

    abstract public function size($file);

    abstract public function multiPartInit($param);

    public function multiPartUpload($param)
    {
        BizException::throws('Not Support');
    }

    public function multiPartUploadEnd($param)
    {
        BizException::throws('Not Support');
    }

    public function updateDriverDomain($data)
    {
        return $data;
    }

    public function domain()
    {
        return '';
    }

    public function domainInternal()
    {
        return '';
    }

    /**
     * 获取路径的完整路径
     * @param $path
     * @return mixed|string
     * 如果path是公共路径（http,https）直接返回
     * 如果是本地路径，返回完整路径，如 /data/xxxxx.xx
     */
    public function getDriverFullPath($path)
    {
        if (empty($path)) {
            return $path;
        }
        if (Str::startsWith($path, '//')) {
            $path = 'http:' . $path;
        } else {
            $path = ltrim($path, '/');
        }
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return config('data.baseUrl', '/') . $path;
    }

    /**
     * 获取路径的内部可用完整路径
     * @param $path
     * @return mixed|string
     * 如果path是公共路径（http,https）直接返回
     * 如果是本地路径，返回完整路径，如 /data/xxxxx.xx
     * @deprecated delete at 2024-04-25 请使用 getDriverFullPath
     */
    public function getDriverFullPathInternal($path)
    {
        if (Str::startsWith($path, '//')) {
            $path = 'http:' . $path;
        } else {
            $path = ltrim($path, '/');
        }
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return config('data.baseUrl', '/') . $path;
    }

    public function repository()
    {
        return $this->repository;
    }

    /**
     * 断点上传相关方法
     */
    protected function multiPartInitToken(array $param)
    {
        $category = $param['category'];
        $file = $param['file'];
        ksort($file, SORT_STRING);
        $configParam = [];
        $configParam['uploadMaxSize'] = EnvUtil::env('uploadMaxSize');
        $hash = md5(serialize($file) . ':' . serialize($configParam));
        $hashFile = self::DATA_CHUNK . '/token/' . $hash . '.php';
        if (file_exists($hashFile)) {
            $file = (include $hashFile);
        } else {
            $file['chunkUploaded'] = 0;
            $file['hash'] = $hash;
            // 计算临时文件路径
            $extension = FileUtil::extension($file['name']);
            $file['path'] = strtolower(Str::random(32)) . '.' . $extension;
            $file['fullPath'] = self::DATA_TEMP . '/' . $category . '/' . $file['path'];
        }
        return $file;
    }

    protected function uploadChunkTokenAndDeleteToken($token)
    {
        $hash = $token['hash'];
        $hashFile = self::DATA_CHUNK . '/token/' . $hash . '.php';
        $this->localStorage->delete($hashFile);
    }

    protected function uploadChunkTokenAndUpdateToken($token)
    {
        $hash = $token['hash'];
        $hashFile = self::DATA_CHUNK . '/token/' . $hash . '.php';
        $this->localStorage->put($hashFile, '<' . '?php return ' . var_export($token, true) . ';');
    }

}
