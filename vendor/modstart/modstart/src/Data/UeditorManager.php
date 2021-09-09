<?php


namespace ModStart\Data;


use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;

class UeditorManager
{
    private static function basicConfig()
    {
        $dataUploadConfig = config('data.upload', []);
        $config = [
                        "imageActionName" => "image",
            "imageFieldName" => "file",
            "imageMaxSize" => $dataUploadConfig['image']['maxSize'],
            "imageAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),
            "imageCompressEnable" => true,
            "imageCompressBorder" => 5000,
            "imageInsertAlign" => "none",
            "imageUrlPrefix" => "",

                        "scrawlActionName" => "crawl",
            "scrawlFieldName" => "file",
            "scrawlMaxSize" => $dataUploadConfig['image']['maxSize'],
            "scrawlUrlPrefix" => "",
            "scrawlInsertAlign" => "none",

                        "snapscreenActionName" => "snap",
            "snapscreenUrlPrefix" => "",
            "snapscreenInsertAlign" => "none",

                        "catcherLocalDomain" => ["127.0.0.1", "localhost"],
            "catcherActionName" => "catch",
            "catcherFieldName" => "source",
            "catcherUrlPrefix" => "",
            "catcherMaxSize" => $dataUploadConfig['image']['maxSize'],
            "catcherAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

                        "videoActionName" => "video",
            "videoFieldName" => "file",
            "videoUrlPrefix" => "",
            "videoMaxSize" => $dataUploadConfig['video']['maxSize'],
            "videoAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['video']['extensions']),

                        "fileActionName" => "file",
            "fileFieldName" => "file",
            "fileUrlPrefix" => "",
            "fileMaxSize" => $dataUploadConfig['file']['maxSize'],
            "fileAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['file']['extensions']),

                        "imageManagerActionName" => "listImage",
            "imageManagerListSize" => 20,
            "imageManagerUrlPrefix" => "",
            "imageManagerInsertAlign" => "none",
            "imageManagerAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

                        "fileManagerActionName" => "listFile",
            "fileManagerUrlPrefix" => "",
            "fileManagerListSize" => 20,
            "fileManagerAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['file']['extensions'])

        ];
        return $config;
    }

    public static function handle($uploadTable, $uploadCategoryTable, $userId, $option = null)
    {
        $config = self::basicConfig();
        $input = InputPackage::buildFromInput();
        $action = $input->getTrimString('action');
        switch ($action) {
            case 'config':
                return Response::jsonRaw($config);
            case 'catch':
                set_time_limit(30);
                $sret = array(
                    'state' => '',
                    'list' => null
                );
                if (AdminPermission::isDemo()) {
                    $sret ['state'] = 'ERROR';
                    return Response::jsonRaw($sret);
                }
                $savelist = array();
                $list = $input->getArray($config ['catcherFieldName']);
                if (empty ($list)) {
                    $sret ['state'] = 'ERROR';
                    return Response::jsonRaw($sret);
                }
                $sret ['state'] = 'SUCCESS';
                foreach ($list as $f) {
                    if (preg_match('/^(http|ftp|https):\\/\\//i', $f)) {
                        $ext = FileUtil::extension($f);
                        if (in_array('.' . $ext, $config ['catcherAllowFiles'])) {
                            if ($imageContent = CurlUtil::getRaw($f)) {
                                $ret = DataManager::upload('image', L('Image') . '.' . $ext, $imageContent, $option);
                                if ($ret['code']) {
                                    $ret['state'] = $ret['msg'];
                                } else {
                                    $data = $ret['data']['data'];
                                    $fullPath = $ret['data']['fullPath'];
                                    ModelUtil::insert($uploadTable, [
                                        'category' => $data['category'],
                                        'dataId' => $data['id'],
                                        'uploadCategoryId' => 0,
                                        'userId' => $userId,
                                    ]);
                                    $savelist [] = array(
                                        'state' => 'SUCCESS',
                                        'url' => $fullPath,
                                        'size' => strlen($imageContent),
                                        'title' => '',
                                        'original' => '',
                                        'source' => htmlspecialchars($f)
                                    );
                                }
                            } else {
                                $ret ['state'] = 'Get remote file error';
                            }
                        } else {
                            $ret ['state'] = 'File ext not allowed';
                        }
                    } else {
                        $savelist [] = array(
                            'state' => 'not remote image',
                            'url' => '',
                            'size' => '',
                            'title' => '',
                            'original' => '',
                            'source' => htmlspecialchars($f)
                        );
                    }
                }
                $sret ['list'] = $savelist;
                return Response::jsonRaw($sret);
        }
    }
}