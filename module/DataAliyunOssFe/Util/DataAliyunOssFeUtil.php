<?php


namespace Module\DataAliyunOssFe\Util;


use ModStart\Core\Input\Response;
use ModStart\Module\ModuleClassLoader;
use OSS\Core\OssException;
use OSS\OssClient;

class DataAliyunOssFeUtil
{
    public static function init()
    {
        static $init = false;
        if ($init) {
            return;
        }
        $init = true;
        ModuleClassLoader::addNamespaceIfMissing('OSS',
            __DIR__ . '/../SDK/vendor/aliyuncs/oss-sdk-php/src/OSS/');
        ModuleClassLoader::addNamespaceIfMissing('Darabonba\\OpenApi',
            __DIR__ . '/../SDK/vendor/alibabacloud/darabonba-openapi/src/');
        // "AlibabaCloud\\SDK\\Sts\\V20150401\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\SDK\\Sts\\V20150401\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/sts-20150401/src/');
        // "AlibabaCloud\\Tea\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\Tea\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/tea/src/');
        // "AlibabaCloud\\Tea\\Utils\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\Tea\\Utils\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/tea-utils/src/');
        // "AlibabaCloud\\Credentials\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\Credentials\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/credentials/src/');
        // "AlibabaCloud\\Endpoint\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\Endpoint\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/endpoint-util/src/');
        // "AlibabaCloud\\OpenApiUtil\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('AlibabaCloud\\OpenApiUtil\\',
            __DIR__ . '/../SDK/vendor/alibabacloud/openapi-util/src/');
        // "Adbar\\": "src"
        ModuleClassLoader::addNamespaceIfMissing('Adbar\\',
            __DIR__ . '/../SDK/vendor/adbario/php-dot-notation/src/');
    }

    private static function client()
    {
        static $client = null;
        if (null === $client) {
            $client = new OssClient(
                modstart_config('DataAliyunOssFe_AccessKeyId'),
                modstart_config('DataAliyunOssFe_AccessKeySecret'),
                modstart_config('DataAliyunOssFe_Endpoint')
            );
        }
        return $client;
    }

    private static function bucket()
    {
        return modstart_config('DataAliyunOssFe_Bucket');
    }

    public static function getUrl($url, $timeout = 3600)
    {
        try {
            $url = self::client()->signUrl(self::bucket(), $url, $timeout);
            return Response::generateSuccessData([
                'url' => $url,
            ]);
        } catch (OssException $e) {
            throw $e;
        }
    }
}
