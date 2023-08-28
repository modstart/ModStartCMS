<?php


namespace ModStart\Data;


use Illuminate\Support\Str;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\EnvUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Data\Event\DataDeletedEvent;
use ModStart\Data\Event\DataFileUploadedEvent;
use ModStart\Data\Storage\FileDataStorage;

/**
 * Class DataManager
 * @package ModStart\Data
 *
 * @example
 * 测试新存储
 * var_dump(DataManager::upload('file', 'test111.txt', 'test', ['driver' => 'DataStorage_DataSFtp']));
 */
class DataManager
{
    /** @var AbstractDataStorage[] */
    private static $storages = [];
    private static $config = null;

    public static function uploadConfig($category)
    {
        if (!is_array($category)) {
            $category = [$category];
        }
        $categoryConfigs = [];
        foreach ($category as $cat) {
            $categoryConfigs[$cat] = config('data.upload.' . $cat);
        }
        return [
            'chunkSize' => EnvUtil::env('uploadMaxSize'),
            'category' => $categoryConfigs,
        ];
    }

    /**
     * 从用户配置中获取文件上传相关配置
     * @return array|null
     */
    public static function getConfigOption()
    {
        static $option = null;
        if (null === $option) {
            $option ['driver'] = modstart_config('DataStorageDefaultDriver', '');
            if (empty($option ['driver'])) {
                $option ['driver'] = app()->config->get('DataStorageDriver');
            }
            if (empty($option ['driver'])) {
                $option ['driver'] = 'DataStorage_DataFile';
                app()->bind('DataStorage_DataFile', function () {
                    return new FileDataStorage(null);
                });
            }
        }
        return $option;
    }

    private static function prepareOption($option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        if (null === self::$config) {
            self::$config = config('data.upload', []);
        }
        $hash = md5(json_encode($option));
        if (isset(self::$storages[$hash])) {
            return $option;
        }
        $map[$option['driver']] = app($option['driver']);
        self::$storages[$hash] = $map[$option['driver']];
        return $option;
    }

    /**
     * @param null $option
     * @return AbstractDataStorage
     * @throws BizException
     */
    public static function storage($option = null)
    {
        if (null === $option) {
            $option = self::prepareOption();
        }
        $hash = md5(json_encode($option));
        BizException::throwsIf('Storage empty', !isset(self::$storages[$hash]));
        return self::$storages[$hash];
    }

    /**
     * 文件上传
     * @param $category
     * @param $input
     * @param array $extra
     * @param null $option
     * @return array
     * @throws \Exception
     */
    public static function uploadHandle($category, $input, $extra = [], $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $action = empty($input['action']) ? '' : $input['action'];
        $file = [];
        foreach (['name', 'type', 'lastModifiedDate', 'size'] as $k) {
            if (empty($input[$k])) {
                return Response::generate(-1, $k . ' empty');
            }
            $file[$k] = $input[$k] . '';
        }
        if (!empty($input['md5'])) {
            $file['md5'] = $input['md5'];
        }
        $file = array_merge($file, $extra);
        if (empty(self::$config[$category])) {
            return Response::generate(-2, 'Unknown category : ' . $category);
        }
        $config = self::$config[$category];
        if (strlen($file['name']) > $storage->repository()->maxFilenameByte()) {
            return Response::generate(-3, 'Filename too long ( max 200 bytes )');
        }
        $extension = FileUtil::extension($file['name']);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, L('File extension %s not permit', $extension));
        }
        if ($file['size'] > $config['maxSize']) {
            return Response::generate(-5, L('File Size Limit %s', FileUtil::formatByte($config['maxSize'])));
        }
        if ('init' == $action) {
            return $storage->multiPartInit([
                'category' => $category,
                'file' => $file,
            ]);
        }
        return $storage->multiPartUpload([
            'category' => $category,
            'file' => $file,
            'input' => $input,
        ]);
    }

    /**
     * 上传文件到 data_temp
     * @param $category string
     * @param $filename string
     * @param $content string
     * @param $option array|null
     * @param $param array
     * @return array
     */
    public static function uploadToTemp($category, $filename, $content, $option = null, $param = [])
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        if (!isset($param['eventOpt'])) {
            $param['eventOpt'] = [];
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        if (empty(self::$config[$category])) {
            return Response::generate(-1, 'Unknown category : ' . $category);
        }
        $config = self::$config[$category];
        if (empty($filename)) {
            return Response::generate(-2, 'Filename empty');
        }
        if (strlen($filename) > $storage->repository()->maxFilenameByte()) {
            return Response::generate(-3, 'Filename too long ( max 200 bytes )');
        }
        $extension = FileUtil::extension($filename);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, L('File extension %s not permit', $extension));
        }
        $size = strlen($content);
        if ($size == 0) {
            return Response::generate(-5, 'File content empty');
        }
        if ($size > $config['maxSize']) {
            return Response::generate(-5, L('File Size Limit %s', FileUtil::formatByte($config['maxSize'])));
        }
        $updateTimestamp = time();
        $retry = 0;
        do {
            $path = strtolower(Str::random(32)) . '.' . $extension;
            $fullPath = AbstractDataStorage::DATA_TEMP . '/' . $category . '/' . $path;
        } while ($retry++ < 10 && $storage->has($fullPath));
        if ($retry >= 10) {
            return Response::generate(-7, 'Upload fail');
        }
        $storage->put($fullPath, $content);
        DataFileUploadedEvent::fire($storage->driverName(), $category, $fullPath, $param['eventOpt']);
        $dataTemp = $storage->repository()->addTemp($category, $path, $filename, $size);
        $path = config('data.baseUrl', '/') . AbstractDataStorage::DATA_TEMP . '/' . $dataTemp['category'] . '/' . $dataTemp['path'];
        $fullPath = $path;
        if (!empty($option['domain'])) {
            $fullPath = $option['domain'] . $path;
        }
        return Response::generateSuccessData([
            'dataTemp' => $dataTemp,
            'path' => $path,
            'fullPath' => $fullPath,
        ]);
    }

    /**
     * 上传文件内容
     * @param string $category
     * @param string $filename 包含后缀名的文件
     * @param string $content
     * @param array|null $option
     * @param array $param
     * @return array [
     *     'data' => [
     *         'id'=>1,
     *     ],
     *     'path' => 'data/image/xxxx.jpg',
     *     'fullPath' => '/data/image/xxx.jpg',
     * ]
     */
    public static function upload($category, $filename, $content, $option = null, $param = [])
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        if (!isset($param['eventOpt'])) {
            $param['eventOpt'] = [];
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        if (empty(self::$config[$category])) {
            return Response::generate(-1, 'Unknown category : ' . $category);
        }
        $config = self::$config[$category];
        if (empty($filename)) {
            return Response::generate(-2, 'Filename empty');
        }
        if (strlen($filename) > $storage->repository()->maxFilenameByte()) {
            return Response::generate(-3, 'Filename too long ( max 200 bytes )');
        }
        $extension = FileUtil::extension($filename);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, L('File extension %s not permit', $extension));
        }
        $size = strlen($content);
        if ($size == 0) {
            return Response::generate(-5, 'File content empty');
        }
        if ($size > $config['maxSize']) {
            return Response::generate(-5, L('File Size Limit %s', FileUtil::formatByte($config['maxSize'])));
        }
        $updateTimestamp = time();
        $retry = 0;
        do {
            $path = date('Y/m/d/', $updateTimestamp) . (time() % 86400) . '_' . strtolower(Str::random(4)) . '_' . mt_rand(1000, 9999) . '.' . $extension;
            $fullPath = AbstractDataStorage::DATA . '/' . $category . '/' . $path;
        } while ($retry++ < 10 && $storage->has($fullPath));
        if ($retry >= 10) {
            return Response::generate(-7, 'Upload fail');
        }
        $storage->put($fullPath, $content);
        DataFileUploadedEvent::fire($storage->driverName(), $category, $fullPath, $param['eventOpt']);
        $md5 = md5($content);
        $data = $storage->repository()->addData($category, $path, $filename, $size, $md5);
        $data = $storage->updateDriverDomain($data);
        $path = config('data.baseUrl', '/') . AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
        $fullPath = $path;
        if (!empty($data['domain'])) {
            $fullPath = $data['domain'] . $path;
        }
        return Response::generateSuccessData([
            'data' => $data,
            'path' => $path,
            'fullPath' => $fullPath,
        ]);
    }

    /**
     * 根据TempData完整路径存储
     * @param $dataTempFullPath
     * @param null $option
     * @return array
     * @throws \Exception
     */
    public static function storeTempDataByPath($dataTempFullPath, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $dataTempFullPath = trim($dataTempFullPath, '/');
        if (preg_match(AbstractDataStorage::PATTERN_DATA_TEMP, $dataTempFullPath, $mat)) {
            return self::storeTempData($mat[1], $mat[2], $option);
        }
        return Response::generate(-1, 'TempPath Invalid', null);
    }

    /**
     * 根据Category和TempData路径存储
     * @param $category
     * @param $tempPath
     * @param null $option
     * @return array
     * @throws \Exception
     */
    public static function storeTempData($category, $dataTempPath, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $dataTemp = $storage->repository()->getTemp($category, $dataTempPath);
        if (empty($dataTemp)) {
            return Response::generate(-1, L('TempPath Not Exists, Please Upload Again'));
        }
        $extension = FileUtil::extension($dataTemp['filename']);
        $updateTimestamp = time();
        $path = date('Y/m/d/', $updateTimestamp) . (time() % 86400) . '_' . strtolower(Str::random(4)) . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $fullPath = AbstractDataStorage::DATA . '/' . $category . '/' . $path;

        $from = AbstractDataStorage::DATA_TEMP . '/' . $dataTemp['category'] . '/' . $dataTemp['path'];
        $to = AbstractDataStorage::DATA . '/' . $dataTemp['category'] . '/' . $path;

        if (!$storage->has($from)) {
            $storage->repository()->deleteTempById($dataTemp['id']);
            return Response::generate(-3, L('TempPath Not Exists, Please Upload Again'));
        }

        $storage->move($from, $to);
        $data = $storage->repository()->addData($dataTemp['category'], $path, $dataTemp['filename'], $dataTemp['size'], $dataTemp['md5']);
        $data = $storage->updateDriverDomain($data);
        $storage->repository()->deleteTempById($dataTemp['id']);
        $path = config('data.baseUrl', '/') . AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
        $fullPath = $path;
        if (!empty($data['domain'])) {
            $fullPath = $data['domain'] . $path;
        }
        return Response::generate(0, 'ok', [
            'data' => $data,
            'path' => $path,
            'fullPath' => $fullPath,
        ]);
    }

    /**
     * 根据ID删除文件（包括物理软删除）
     * @param $id
     * @param $option
     */
    public static function deleteById($id, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $data = $storage->repository()->getDataById($id);
        if (empty($data)) return;
        $file = AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
        if ($storage->has($file)) {
            $storage->softDelete($file);
        }
        $storage->repository()->deleteDataById($id);
        DataDeletedEvent::fire($data);
    }

    /**
     * 根据路径删除
     *
     * @param $path
     * @param null $option
     * @throws \Exception
     */
    public static function deleteByPath($path, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $data = $storage->repository()->getDataByPath($path);
        if (empty($data)) {
            return;
        }
        $file = AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
        if ($storage->has($file)) {
            $storage->softDelete($file);
        }
        $storage->repository()->deleteDataById($data['id']);
        DataDeletedEvent::fire($data);
    }

    /**
     * 根据路径删除DataTemp
     *
     * @param $tempDataPath
     * @param null $option
     * @throws \Exception
     */
    public static function deleteDataTempByPath($tempDataPath, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $dataTemp = $storage->repository()->getTempByPath($tempDataPath);
        if (empty($dataTemp)) return;
        $storage->delete($tempDataPath);
        $storage->repository()->deleteTempById($dataTemp['id']);
    }


    /**
     * 解析已上传文件路径
     *
     * @param $url 文件路径 /data/xxxxxxx.xxx http://xxx.com/data/xxxxxxx.xxx
     * @return array
     */
    public static function parseDataUrl($url)
    {
        if (preg_match(AbstractDataStorage::PATTERN_DATA_STRING, $url, $mat)) {
            return Response::generateSuccessData([
                'url' => $mat[0],
                'category' => $mat[1],
                'path' => $mat[2],
            ]);
        }
        return Response::generateError('parse error');
    }


    /**
     * 准备文件到本地可用
     * @param $path string 文件路径 /data/xxxxxxx.xxx data_temp/xxxxxx.xxx /data_temp/xxxxxxx.xxx http://www.example.com/data/xxxxx.xxx
     * @param $option
     * @return array
     */
    public static function preparePathForLocal($path, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $fileFullPath = $storage->getDriverFullPath($path);
        $localFile = FileUtil::savePathToLocalTemp($fileFullPath, '.' . FileUtil::extension($path));
        if (!file_exists($localFile)) {
            return Response::generate(-1, L('Safe File Error') . ' - ' . $path);
        }
        return Response::generate(0, null, [
            'path' => $localFile,
            'name' => basename($localFile),
        ]);
    }


    /**
     * 准备文件到本地可用（使用内网域名）
     * @param $path string 文件路径 /data/xxxxxxx.xxx /data_temp/xxxxxxx.xxx http://www.example.com/data/xxxxx.xxx
     * @param $option
     * @return array
     * @throws
     */
    public static function preparePathInternalForLocal($path, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $fileFullPath = $storage->getDriverFullPathInternal($path);
        $localFile = FileUtil::savePathToLocalTemp($fileFullPath, '.' . FileUtil::extension($path));
        if (!file_exists($localFile)) {
            return Response::generate(-1, L('Safe File Error') . ' - ' . $path);
        }
        $base = public_path('');
        return Response::generate(0, null, [
            'path' => $localFile,
            'baseUrl' => ltrim(str_replace('\\', '/', substr($localFile, strlen($base))), '/\\'),
            'name' => basename($localFile),
        ]);
    }

    public static function getDataTempFileContent($tempDataPath, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $fileFullPath = $storage->getDriverFullPath($tempDataPath);
        $localFile = FileUtil::savePathToLocalTemp($fileFullPath, '.' . FileUtil::extension($tempDataPath));
        if (!file_exists($localFile)) {
            return null;
        }
        $content = file_get_contents($localFile);
        @unlink($localFile);
        return $content;
    }

    public static function isDataTemp($path)
    {
        return preg_match(AbstractDataStorage::PATTERN_DATA_TEMP, $path);
    }

    public static function fix($path, $option = null)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        return AssetsUtil::fix($storage->getDriverFullPath($path), false);
    }

    public static function fixFull($path, $option = null)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        return AssetsUtil::fixFull($storage->getDriverFullPath($path), false);
    }

}
