<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;

class EncodeUtil
{
    public static function expiredDataForever($string, $key = null)
    {
        return self::expiredData($string, $key, 0);
    }

    public static function expiredData($string, $key = null, $expireSeconds = 3600)
    {
        if (is_null($key)) {
            $key = SecureUtil::encryptKey();
        }
        $stringHex = bin2hex($string);
        $nonce = strtolower(Str::random(6));
        $timestampHex = dechex(time());
        $expireSecondsHex = dechex($expireSeconds);
        $sign = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        $param = [];
        $param[] = $stringHex;
        $param[] = $nonce;
        $param[] = $timestampHex;
        $param[] = $expireSecondsHex;
        $param[] = $sign;
        return join('_', $param);
    }

    /**
     * @param $string
     * @param $key
     * @return mixed
     */
    public static function expiredDataDecode($string, $key = null)
    {
        $p = explode('_', $string);
        if (count($p) != 5) {
            return null;
        }
        if (is_null($key)) {
            $key = SecureUtil::encryptKey();
        }
        $stringHex = $p[0];
        $nonce = $p[1];
        $timestampHex = $p[2];
        $expireSecondsHex = $p[3];
        $sign = $p[4];
        $signCalc = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        if ($sign != $signCalc) {
            return null;
        }
        $expireSeconds = hexdec($expireSecondsHex);
        if ($expireSeconds > 0) {
            $timestamp = hexdec($timestampHex);
            if (time() > $timestamp + $expireSeconds) {
                return null;
            }
        }
        return hex2bin($stringHex);
    }

    public static function md5WithSalt($password, $passwordSalt)
    {
        return md5(md5($password) . md5($passwordSalt));
    }

    public static function detectCharset($content, $checks = ['gbk', 'utf-8'])
    {
        $encoding = strtolower(mb_detect_encoding($content, $checks));
        switch ($encoding) {
            case 'cp936':
                return 'gbk';
            case 'utf-8':
                return 'utf-8';
            default:
                return $encoding;
        }
    }

    public static function toUTF8($content, $froms = ['gbk', 'utf-8'])
    {
        $encoding = mb_detect_encoding($content, $froms);
        return iconv($encoding, 'UTF-8', $content);
    }

    public static function base64UrlSafeEncode($str)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
    }

    public static function base64UrlSafeDecode($str)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $str));
    }

    public static function fileXzipEncode($pathname)
    {
        if (!file_exists($pathname)) {
            throw new \Exception("Input file not found: {$pathname}");
        }
        // Generate new filepath with .xzip extension
        $pathInfo = pathinfo($pathname);
        $basePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $outputPath = $basePath . '.xzip';

        // Get file info
        $fileSize = filesize($pathname);
        $fileExt = FileUtil::extension($pathname);

        // Generate random 16-character key
        $encryptionKey = Str::random(16);

        // Create metadata
        $fileMeta = [
            "version" => 1,
            "format" => $fileExt,
            "size" => $fileSize,
            "key" => $encryptionKey
        ];

        // Convert metadata to JSON and then base64 encode
        $metaJson = json_encode($fileMeta);
        $metaB64 = base64_encode($metaJson);
        $metaLength = strlen($metaB64);

        // Prepare encryption key
        $keyBytes = $encryptionKey;
        $keyLength = strlen($keyBytes);

        // Stream processing: read, encrypt and write in chunks
        $chunkSize = 8192; // 8KB chunks to avoid memory issues
        $inputHandle = fopen($pathname, 'rb');
        $outputHandle = fopen($outputPath, 'wb');

        try {
            // Write metadata length (4 bytes, little-endian)
            fwrite($outputHandle, pack('V', $metaLength));
            // Write base64 encoded metadata
            fwrite($outputHandle, $metaB64);

            // Stream encrypt the file content
            $bytesProcessed = 0;
            while (!feof($inputHandle)) {
                $chunk = fread($inputHandle, $chunkSize);
                if ($chunk === false) {
                    break;
                }

                // XOR encrypt the chunk
                $encryptedChunk = '';
                for ($i = 0; $i < strlen($chunk); $i++) {
                    $encryptedChunk .= chr(ord($chunk[$i]) ^ ord($keyBytes[$bytesProcessed % $keyLength]));
                    $bytesProcessed++;
                }

                // Write encrypted chunk
                fwrite($outputHandle, $encryptedChunk);
            }
        } finally {
            fclose($inputHandle);
            fclose($outputHandle);
        }

        LogUtil::info('EncodeUtil.fileXzipEncode', [
            'input' => $pathname,
            'output' => $outputPath,
            'originalSize' => $fileSize,
            'metaLength' => $metaLength
        ]);

        return $outputPath;
    }

    public static function fileXzipDecode($pathname)
    {
        if (!file_exists($pathname)) {
            throw new \Exception("Input xzip file not found: {$pathname}");
        }
        if (!Str::endsWith($pathname, '.xzip')) {
            return $pathname; // Not a xzip file, return as is
        }
        $inputHandle = fopen($pathname, 'rb');
        try {
            // Read metadata length (first 4 bytes)
            $metaLengthBytes = fread($inputHandle, 4);
            if (strlen($metaLengthBytes) != 4) {
                throw new \Exception("Invalid xzip file: cannot read metadata length");
            }
            $metaLength = unpack('V', $metaLengthBytes)[1];
            // Read and decode metadata
            $metaB64 = fread($inputHandle, $metaLength);
            if (strlen($metaB64) != $metaLength) {
                throw new \Exception("Invalid xzip file: cannot read metadata");
            }

            try {
                $metaJson = base64_decode($metaB64);
                $fileMeta = json_decode($metaJson, true);
            } catch (\Exception $e) {
                throw new \Exception("Invalid xzip file: cannot decode metadata - " . $e->getMessage());
            }
            // Validate metadata
            if (!isset($fileMeta['format'], $fileMeta['size'], $fileMeta['key'])) {
                throw new \Exception("Invalid xzip file: missing required metadata fields");
            }
        } finally {
            fclose($inputHandle);
        }
        // Generate output filepath
        $pathInfo = pathinfo($pathname);
        $basePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $outputPath = $basePath . '.' . $fileMeta['format'];
        // Prepare decryption key
        $keyBytes = $fileMeta['key'];
        $keyLength = strlen($keyBytes);
        // Stream processing: read, decrypt and write in chunks
        $chunkSize = 8192; // 8KB chunks to avoid memory issues
        $bytesProcessed = 0;
        $actualSize = 0;
        $inputHandle = fopen($pathname, 'rb');
        $outputHandle = fopen($outputPath, 'wb');
        try {
            // Skip metadata (already read above)
            fseek($inputHandle, 4 + $metaLength);
            // Stream decrypt the file content
            while (!feof($inputHandle)) {
                $chunk = fread($inputHandle, $chunkSize);
                if ($chunk === false) {
                    break;
                }
                // XOR decrypt the chunk
                $decryptedChunk = '';
                for ($i = 0; $i < strlen($chunk); $i++) {
                    $decryptedChunk .= chr(ord($chunk[$i]) ^ ord($keyBytes[$bytesProcessed % $keyLength]));
                    $bytesProcessed++;
                }

                // Write decrypted chunk
                fwrite($outputHandle, $decryptedChunk);
                $actualSize += strlen($decryptedChunk);
            }
        } finally {
            fclose($inputHandle);
            fclose($outputHandle);
        }

        // Verify file size
        if ($actualSize != $fileMeta['size']) {
            LogUtil::info('EncodeUtil.fileXzipDecode.SizeWarning', [
                'expected' => $fileMeta['size'],
                'actual' => $actualSize
            ]);
        }
        return $outputPath;
    }

    public static function compressEncode($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $data = gzcompress($data);
        return base64_encode($data);
    }

    public static function compressDecode($data)
    {
        $data = @base64_decode($data);
        if (empty($data)) {
            return null;
        }
        $data = @gzuncompress($data);
        if (empty($data)) {
            return null;
        }
        return @json_decode($data, true);
    }
}
