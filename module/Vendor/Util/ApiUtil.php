<?php


namespace Module\Vendor\Util;

use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\EnvUtil;
use ModStart\Module\ModuleManager;

class ApiUtil
{
    public static function config()
    {
        $config = modstart_config();

        $data = [];

        // 基础
        $data['siteBase'] = Request::domainUrl();
        $data['siteLogo'] = AssetsUtil::fixFull($config->get('siteLogo'));
        $data['siteName'] = $config->get('siteName');
        $data['siteSlogan'] = $config->get('siteSlogan');
        $data['siteDomain'] = $config->get('siteDomain');
        $data['siteKeywords'] = $config->get('siteKeywords');
        $data['siteDescription'] = $config->get('siteDescription');
        $data['siteFavIco'] = AssetsUtil::fixFull($config->get('siteFavIco'));
        $data['siteBeian'] = $config->get('siteBeian');
        $data['siteCDN'] = AssetsUtil::fixFull(AssetsUtil::cdn(), false);

        $data['modules'] = ModuleManager::listAllEnableModuleNames();

        // 支付
        /** @deprecated delete after 2023-09-27 */
        $data['payAlipayOn'] = $config->getBoolean('payAlipayOn');
        $data['payAlipayWebOn'] = $config->getBoolean('payAlipayWebOn');
        $data['payWechatOn'] = $config->getBoolean('payWechatOn');
        $data['payMemberMoneyOn'] = $config->getBoolean('payMemberMoneyOn');

        // 上传
        $data['dataUpload'] = [];
        $data['dataUpload'] = [
            'chunkSize' => EnvUtil::env('uploadMaxSize'),
            'category' => [],
        ];
        $uploads = config('data.upload');
        foreach ($uploads as $category => $categoryInfo) {
            $data['dataUpload']['category'][$category] = [
                'maxSize' => $categoryInfo['maxSize'],
                'extensions' => $categoryInfo['extensions'],
            ];
        }

        return $data;
    }
}
