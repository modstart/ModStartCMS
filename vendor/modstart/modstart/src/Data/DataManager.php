<?php


namespace ModStart\Data;


use Illuminate\Support\Str;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\Data\Storage\FileDataStorage;

class DataManager
{
    
    private static $storages = [];
    private static $config = null;

    
    public static function getConfigOption()
    {
        static $option = null;
        if (null === $option) {
            $option ['driver'] = app()->config->get('DataStorageDriver');
            if (empty($option ['driver'])) {
                $option ['driver'] = 'DataStorage_File';
                app()->bind('DataStorage_File', function () {
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

    
    public static function storage($option = null)
    {
        if (null === $option) {
            $option = self::prepareOption();
        }
        $hash = md5(json_encode($option));
        BizException::throwsIf('Storage empty', !isset(self::$storages[$hash]));
        return self::$storages[$hash];
    }

    
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

    
    public static function uploadToTemp($category, $filename, $content, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
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
        $dataTemp = $storage->repository()->addTemp($category, $path, $filename, $size);
        $path = '/' . AbstractDataStorage::DATA_TEMP . '/' . $dataTemp['category'] . '/' . $dataTemp['path'];
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

    
    public static function upload($category, $filename, $content, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
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
        $data = $storage->repository()->addData($category, $path, $filename, $size);
        $data = $storage->updateDriverDomain($data);
        $path = '/' . AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
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

    
    public static function storeTempData($category, $dataTempPath, $option = null)
    {
        if (null === $option) {
            $option = self::getConfigOption();
        }
        $option = self::prepareOption($option);
        $storage = self::storage($option);
        $dataTemp = $storage->repository()->getTemp($category, $dataTempPath);
        if (empty($dataTemp)) {
            return Response::generate(-1, 'TempPath not exists');
        }
        $extension = FileUtil::extension($dataTemp['filename']);
        $updateTimestamp = time();
        $path = date('Y/m/d/', $updateTimestamp) . (time() % 86400) . '_' . strtolower(Str::random(4)) . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $fullPath = AbstractDataStorage::DATA . '/' . $category . '/' . $path;

        $from = AbstractDataStorage::DATA_TEMP . '/' . $dataTemp['category'] . '/' . $dataTemp['path'];
        $to = AbstractDataStorage::DATA . '/' . $dataTemp['category'] . '/' . $path;

        if (!$storage->has($from)) {
            $storage->repository()->deleteTempById($dataTemp['id']);
            return Response::generate(-3, 'TempPath not exists');
        }

        $storage->move($from, $to);
        $data = $storage->repository()->addData($dataTemp['category'], $path, $dataTemp['filename'], $dataTemp['size']);
        $data = $storage->updateDriverDomain($data);
        $storage->repository()->deleteTempById($dataTemp['id']);
        $path = '/' . AbstractDataStorage::DATA . '/' . $data['category'] . '/' . $data['path'];
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
        $storage->softDelete($file);
        $storage->repository()->deleteDataById($id);
    }

    
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
    }

    
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
            return Response::generate(-1, 'Save file fail');
        }
        return Response::generate(0, null, [
            'path' => $localFile,
            'name' => basename($localFile),
        ]);
    }


    
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
            return Response::generate(-1, 'Save file fail');
        }
        return Response::generate(0, null, [
            'path' => $localFile,
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
