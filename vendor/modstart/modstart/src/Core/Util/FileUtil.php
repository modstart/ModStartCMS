<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Util 文件
 */
class FileUtil
{
    private static $mimeMap = [
        'aac' => 'audio/aac',
        'abw' => 'application/x-abiword',
        'arc' => 'application/x-freearc',
        'avi' => 'video/x-msvideo',
        'azw' => 'application/vnd.amazon.ebook',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'bz' => 'application/x-bzip',
        'bz2' => 'application/x-bzip2',
        'cda' => 'application/x-cdf',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'eot' => 'application/vnd.ms-fontobject',
        'epub' => 'application/epub+zip',
        'gz' => 'application/gzip',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/vnd.microsoft.icon',
        'ics' => 'text/calendar',
        'jar' => 'application/java-archive',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'jsonld' => 'application/ld+json',
        'mid .midi' => 'audio/midi',
        'mjs' => 'text/javascript',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'mpkg' => 'application/vnd.apple.installer+xml',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'oga' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'ogx' => 'application/ogg',
        'opus' => 'audio/opus',
        'otf' => 'font/otf',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
        'php' => 'application/x-httpd-php',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'rar' => 'application/vnd.rar',
        'rtf' => 'application/rtf',
        'sh' => 'application/x-sh',
        'svg' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        'tar' => 'application/x-tar',
        'tif .tiff' => 'image/tiff',
        'ts' => 'video/mp2t',
        'ttf' => 'font/ttf',
        'txt' => 'text/plain',
        'vsd' => 'application/vnd.visio',
        'wav' => 'audio/wav',
        'weba' => 'audio/webm',
        'webm' => 'video/webm',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml' => 'application/xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'zip' => 'application/zip',
        '3gp' => 'video/3gpp',
        '3g2' => 'video/3gpp2',
        '7z' => 'application/x-7z-compressed',
    ];

    /**
     * @Util 根据文件后缀获取MIME类型字符串
     * @param $ext string 文件后缀
     * @return string|null
     */
    public static function mime($ext)
    {
        $ext = strtolower($ext);
        return isset(self::$mimeMap[$ext]) ? self::$mimeMap[$ext] : null;
    }

    /**
     * @Util 根据MIME类型字符串获取文件后缀
     * @param $mime string MIME类型字符串
     * @return string|null
     */
    public static function mimeToExt($mime)
    {
        $mime = strtolower($mime);
        foreach (self::$mimeMap as $ext => $m) {
            if ($mime == $m) {
                return $ext;
            }
        }
        return null;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public static function getUploadFileNameWithExt($file)
    {
        $filename = $file->getClientOriginalName();
        if ('blob' == $filename) {
            $ext = FileUtil::mimeToExt($file->getMimeType());
            if ($ext) {
                $filename = 'blob.' . $ext;
            } else {
                BizException::throws('获取到的文件名称为空');
            }
        }
        return $filename;
    }

    public static function filePathWritableCheck($paths)
    {
        if (empty($paths)) {
            return Response::generateSuccess();
        }
        $paths = array_unique(array_map(function ($f) {
            return dirname($f);
        }, $paths));
        $paths = array_filter($paths, function ($f) use ($paths) {
            foreach ($paths as $ff) {
                if ($ff != $f && starts_with($ff, $f)) {
                    return false;
                }
            }
            return true;
        });
        if (empty($paths)) {
            return Response::generateSuccess();
        }
        foreach ($paths as $file) {
            $checkFile = base_path($file . '/._write_check_');
            if (false === FileUtil::write($checkFile, 'ok')) {
                return Response::generate(-1, '目录不可写：' . $file);
            }
            // echo "$checkFile\n";
            if (!file_exists($checkFile)) {
                return Response::generateError('目录' . $file . '测试写入失败，请检查权限');
            }
            @unlink($checkFile);
        }
        return Response::generateSuccess();
    }

    /**
     * @Util 写入文件
     * @param $path string
     * @param $content string
     * @return bool 是否写入成功
     */
    public static function write($path, $content)
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            try {
                mkdir($dir, 0755, true);
            } catch (\Exception $e) {
                Log::error('mkdir ' . $dir . ' failed');
                return false;
            }
        }
        try {
            return file_put_contents($path, $content) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @Util 获取文件后缀
     * @param $pathname string 文件路径
     * @return string
     */
    public static function extension($pathname)
    {
        $ext = strtolower(pathinfo($pathname, PATHINFO_EXTENSION));
        $i = strpos($ext, '?');
        if (false !== $i) {
            return substr($ext, 0, $i);
        }
        return $ext;
    }

    public static function isPathCategory($pathname, $category)
    {
        $ext = self::extension($pathname);
        return in_array($ext, config('data.upload.' . $category . '.extensions'));
    }

    public static function arrayToCSVString($list)
    {
        $lines = [];
        foreach ($list as $item) {
            $line = [];
            foreach ($item as $v) {
                $line[] = '"' . str_replace('"', '""', $v) . '",';
            }
            $lines[] = join("", $line);
        }
        return chr(239) . chr(187) . chr(191) . join("\r\n", $lines);
    }

    /**
     * @Util 递归列出目录所有文件
     * @param $dir string 目录
     * @param $filter Closure 过滤器，为空表示不过滤
     * @return array
     */
    public static function listAllFiles($dir, $filter = null, &$results = array(), $prefix = '')
    {
        $files = self::listFiles($dir, '*|.*');
        foreach ($files as $file) {
            if (null !== $filter && !call_user_func($filter, $file)) {
                continue;
            }
            if ($file['isDir']) {
                self::listAllFiles($file['path'] . '/' . $file['filename'], $filter, $results, $prefix ? $prefix . DIRECTORY_SEPARATOR . $file['filename'] : $file['filename']);
            }
            $file['filename'] = $prefix ? $prefix . DIRECTORY_SEPARATOR . $file['filename'] : $file['filename'];
            $results[] = $file;
        }
        return $results;
    }

    /**
     * @Util 列出目录所有文件
     * @param $filename string
     * @param $pattern string 后缀过滤，如 *.txt *.php 等
     * @return array
     */
    public static function listFiles($filename, $pattern = '*')
    {
        if (strpos($pattern, '|') !== false) {
            $patterns = explode('|', $pattern);
        } else {
            $patterns [0] = $pattern;
        }
        $i = 0;
        $dir = array();
        if (is_dir($filename)) {
            $filename = rtrim($filename, '/\\') . '/';
        }
        foreach ($patterns as $pattern) {
            $list = glob($filename . $pattern);
            if ($list !== false) {
                foreach ($list as $file) {
                    $f = basename($file);
                    if ($f === '..' || $f === '.') {
                        continue;
                    }
                    $dir [$i] ['filename'] = $f;
                    $dir [$i] ['path'] = dirname($file);
                    $dir [$i] ['pathname'] = realpath($file);
                    $dir [$i] ['owner'] = @fileowner($file);
                    $dir [$i] ['perms'] = substr(base_convert(@fileperms($file), 10, 8), -4);
                    $dir [$i] ['atime'] = @fileatime($file);
                    $dir [$i] ['ctime'] = @filectime($file);
                    $dir [$i] ['mtime'] = @filemtime($file);
                    $dir [$i] ['size'] = @filesize($file);
                    $dir [$i] ['type'] = @filetype($file);
                    $dir [$i] ['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                    $dir [$i] ['isDir'] = is_dir($file);
                    $dir [$i] ['isFile'] = is_file($file);
                    $dir [$i] ['isLink'] = is_link($file);
                    $dir [$i] ['isReadable'] = is_readable($file);
                    $dir [$i] ['isWritable'] = is_writable($file);
                    $i++;
                }
            }
        }
        usort($dir, function ($a, $b) {
            if (($a["isDir"] && $b["isDir"]) || (!$a["isDir"] && !$b["isDir"])) {
                return $a["filename"] > $b["filename"] ? 1 : -1;
            } else {
                if ($a["isDir"]) {
                    return -1;
                } else if ($b["isDir"]) {
                    return 1;
                }
                if ($a["filename"] == $b["filename"]) return 0;
                return $a["filename"] > $b["filename"] ? -1 : 1;
            }
        });
        return $dir;
    }

    public static function nameWithoutExtension($pathname)
    {
        $pathname = self::name($pathname);
        $i = strrpos($pathname, '.');
        if (false !== $i) {
            return substr($pathname, 0, $i);
        }
        return $pathname;
    }

    public static function name($pathname)
    {
        return pathinfo($pathname, PATHINFO_BASENAME);
    }

    /**
     * @Util 格式化字节
     * @param $bytes int 字节数
     * @param $decimals int 小数最多保留位数，默认为2
     * @return string
     * @example
     * // 返回 1 MB
     * FileUtil::formatByte(1024*1024)
     * // 返回 1.5 GB
     * FileUtil::formatByte(1024*1024*1024*1.5)
     */
    public static function formatByte($bytes, $decimals = 2)
    {
        $size = sprintf("%u", $bytes);
        if ($size == 0) {
            return ("0B");
        }
        $units = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), $decimals) . $units[$i];
    }

    /**
     * @Util 格式化字节（简化）
     * @param $bytes int 字节数
     * @param $decimals int 小数最多保留位数，默认为2
     * @return string
     * @example
     * // 返回 1 M
     * FileUtil::formatByte(1024*1024)
     * // 返回 1.5 G
     * FileUtil::formatByte(1024*1024*1024*1.5)
     */
    public static function formatByteSimple($bytes, $decimals = 2)
    {
        $size = sprintf("%u", $bytes);
        if ($size == 0) {
            return ("0B");
        }
        $units = ["B", "K", "M", "G", "T", "P", "E", "Z", "Y"];
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), $decimals) . $units[$i];
    }

    /**
     * @Util 格式化的文件大小转换为字节
     * @param $sizeString string 如 1M
     * @return int
     */
    public static function formattedSizeToBytes($sizeString)
    {
        $sizeString = strtolower($sizeString);
        $unit = preg_replace('/[^a-z]/', '', $sizeString);
        $value = floatval(preg_replace('/[^0-9.]/', '', $sizeString));

        $units = array('b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4, 'k' => 1, 'm' => 2, 'g' => 3, 't' => 4);
        $exponent = isset($units[$unit]) ? $units[$unit] : 0;

        return intval($value * pow(1024, $exponent));
    }

    public static function getAndEnsurePathnameFolder($pathname)
    {
        $base = dirname($pathname);
        if (!file_exists($base)) {
            @mkdir($base, 0755, true);
        }
        return trim($base, '/') . '/';
    }

    public static function getPathnameFilename($pathname, $extension = true)
    {
        $pathInfo = pathinfo($pathname);
        return ($extension ? $pathInfo['basename'] : $pathInfo['filename']);
    }

    public static function ensureFilepathDir($pathname)
    {
        $dir = dirname($pathname);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    public static function number2dir($id, $depth = 3)
    {
        $width = $depth * 3;
        $idFormated = sprintf('%0' . $width . 'd', $id);
        $dirs = [];
        for ($i = 0; $i < $depth; $i++) {
            $dirs[] = substr($idFormated, $i * 3, 3);
        }
        return join('/', $dirs);
    }

    /**
     * @Util 复制目录
     * @param $src string 源路径，必须给出，不能为空
     * @param $dst string 源路径，必须给出，不能为空
     * @param $replaceExt string|null 如果文件存在需要添加的后缀名，作为备份使用，如果不传表示不备份
     * @param $callback Closure|null 复制回调
     * @param $filter Closure|null 复制过滤器
     * @return null 注意：src 和 dst 如果是文件，需同时是文件，如果是目录，需同时是目录
     */
    public static function copy($src, $dst, $replaceExt = null, $callback = null, $filter = null)
    {
        if (!file_exists($src)) {
            return;
        }
        if (is_file($src)) {
            if (!$filter || call_user_func($filter, $src, $dst)) {
                if (file_exists($dst) && md5_file($src) == md5_file($dst)) {
                    return;
                }
                if (null !== $replaceExt && file_exists($dst)) {
                    @rename($dst, $dst . $replaceExt);
                }
                if (!file_exists($dir = dirname($dst))) {
                    @mkdir($dir, 0755, true);
                }
                // echo "COPY: ${src} -> ${dst}\n";
                if ($callback) {
                    call_user_func($callback, $src, $dst);
                }
                copy($src, $dst);
            }
            return;
        } else {
            if (!$filter || call_user_func($filter, $src, $dst)) {
            } else {
                return;
            }
        }
        $src = rtrim($src, '/') . '/';
        $dst = rtrim($dst, '/') . '/';
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . $file)) {
                    self::copy($src . $file . '/', $dst . $file . '/', $replaceExt, $callback, $filter);
                } else {
                    if (!$filter || call_user_func($filter, $src . $file, $dst . $file)) {
                        if (file_exists($dst . $file) && md5_file($dst . $file) == md5_file($src . $file)) {
                            continue;
                        }
                        if (null !== $replaceExt && file_exists($dst . $file)) {
                            @rename($dst . $file, $dst . $file . $replaceExt);
                        }
                        // echo "COPY: ${src}${file} -> ${dst}${file}\n";
                        if ($callback) {
                            call_user_func($callback, $src . $file, $dst . $file);
                        }
                        copy($src . $file, $dst . $file);
                    }
                }
            }
        }
        closedir($dir);
    }

    /**
     * @Util 删除目录
     *
     * @param $dir string 目录
     * @param $removeSelf bool 是否删除本身
     * @return bool
     */
    public static function rm($dir, $removeSelf = true)
    {
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $fullPath = rtrim($dir, '/\\') . '/' . $file;
                    if (is_dir($fullPath)) {
                        self::rm($fullPath, true);
                    } else {
                        @unlink($fullPath);
                    }
                }
            }
            closedir($dh);
            if ($removeSelf) {
                @rmdir($dir);
            }
        } else {
            @unlink($dir);
        }
        return true;
    }

    public static function canSafeCleanTemp($path)
    {
        $tempPath = public_path('temp');
        $path = realpath($path);
        if (strpos($path, $tempPath) === 0) {
            return true;
        }
        return false;
    }

    /**
     * 删除使用 savePathToLocalTemp 或 generateLocalTempPath 产生的本地临时路径
     * @param $path string
     */
    public static function safeCleanLocalTemp($path)
    {
        if (empty($path)) {
            return;
        }
        $tempPath = public_path('temp');
        if (starts_with($path, $tempPath)) {
            @unlink($path);
        }
    }

    public static function safePath($path, $permit = ['public/temp', 'public/data'])
    {
        $path = realpath($path);
        if (empty($path)) {
            // realpath() returns false on failure, e.g. if the file does not exist.
            BizException::throws('FileSafePath File Not Exists');
        }
        $whiteList = [];
        foreach ($permit as $p) {
            $whiteList[] = realpath(base_path($p));
        }
        $whiteList = array_map(function ($p) {
            if (PlatformUtil::isWindows()) {
                return str_replace('/', '\\', $p);
            } else {
                return str_replace('\\', '/', $p);
            }
        }, $whiteList);
        $found = false;
        foreach ($whiteList as $item) {
            if (starts_with($path, $item)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            BizException::throws('FileSafePath Not Permit');
        }
        return $path;
    }

    /**
     * Safe get user generated file content
     * @param $path
     * @return false|string
     * @throws BizException
     */
    public static function safeGetContent($path, $permit = ['public/temp', 'public/data'])
    {
        $path = self::safePath($path, $permit);
        return file_get_contents($path);
    }

    /**
     * 将远程文件保存为本地可用
     * @param $path string 可以为 http://example.com/xxxxx.xxx /data/xxxxx.xxx
     * @param string $ext 文件后缀
     * @param string $downloadStream 是否使用流下载
     * @return string|null 返回本地临时路径或本地文件绝对路径，注意使用safeCleanLocalTemp来清理文件，如果是本地其他路径可能会误删
     */
    public static function savePathToLocalTemp($path, $ext = null, $downloadStream = false)
    {
        if (@file_exists($path)) {
            return realpath($path);
        }
        if (empty($ext)) {
            $ext = self::extension($path);
        }
        $appKey = config('env.APP_KEY');
        $tempPath = public_path('temp/' . md5($appKey . ':' . $path) . (starts_with($ext, '.') ? $ext : '.' . $ext));
        if (file_exists($tempPath)) {
            return $tempPath;
        }
        if (StrUtil::startWith($path, 'http://') || StrUtil::startWith($path, 'https://') || StrUtil::startWith($path, '//')) {
            if (StrUtil::startWith($path, '//')) {
                $path = 'http://' . $path;
            }
            @mkdir(public_path('temp'));
            if ($downloadStream) {
                $f = @fopen($path, 'r');
                if ($f) {
                    file_put_contents($tempPath, $f);
                }
            } else {
                $content = CurlUtil::getRaw($path, [], [
                    'timeout' => 60 * 10,
                ]);
                if (empty($content)) {
                    return null;
                }
                file_put_contents($tempPath, $content);
            }
            if (!file_exists($tempPath)) {
                return null;
            }
        } else {
            if (StrUtil::startWith($path, '/')) {
                $path = substr($path, 1);
            }
            $tempPath = public_path($path);
        }
        if (!file_exists($tempPath)) {
            return null;
        }
        return $tempPath;
    }

    /**
     * 产生一个本地临时路径
     *
     * @param $ext string 文件后缀
     * @param $hash string 用于产生唯一路径的hash，相同的hash会产生相同的路径
     * @param $realpath bool 是否返回绝对路径
     * @return string
     * @throws BizException
     */
    public static function generateLocalTempPath($ext = 'tmp', $hash = null, $realpath = true)
    {
        if (!file_exists(public_path('temp'))) {
            @mkdir(public_path('temp'));
        }
        if (empty($hash)) {
            for ($i = 0; $i < 10; $i++) {
                $p = 'temp/' . RandomUtil::lowerString(32) . '.' . $ext;
                $tempPath = public_path($p);
                if (!file_exists($tempPath)) {
                    return $realpath ? $tempPath : $p;
                }
            }
            BizException::throws('FileUtil generateLocalTempPath error');
        }
        $appKey = config('env.APP_KEY');
        $p = 'temp/' . md5($appKey . ':' . $hash) . '.' . $ext;
        return $realpath ? public_path($p) : $p;
    }

}
