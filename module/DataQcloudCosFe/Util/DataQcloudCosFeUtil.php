<?php


namespace Module\DataQcloudCosFe\Util;


use ModStart\Module\ModuleClassLoader;

class DataQcloudCosFeUtil
{
    public static function init()
    {
        static $init = false;
        if ($init) {
            return;
        }
        $init = true;
        // Qcloud\Cos
        ModuleClassLoader::addNamespaceIfMissing('Qcloud\\Cos', __DIR__ . '/../SDK/vendor/qcloud/cos-sdk-v5/src/');
        // QCloud\COSSTS
        ModuleClassLoader::addNamespaceIfMissing('QCloud\\COSSTS', __DIR__ . '/../SDK/vendor/qcloud_sts/qcloud-sts-sdk/src/');
        // GuzzleHttp\Command
        ModuleClassLoader::addNamespaceIfMissing('GuzzleHttp\\Command', __DIR__ . '/../SDK/vendor/guzzlehttp/command/src/');
        // GuzzleHttp\Command\Guzzle
        ModuleClassLoader::addNamespaceIfMissing('GuzzleHttp\\Command\\Guzzle', __DIR__ . '/../SDK/vendor/guzzlehttp/guzzle-services/src/');
        include __DIR__ . '/../SDK/vendor/qcloud/cos-sdk-v5/src/Common.php';
    }
}
