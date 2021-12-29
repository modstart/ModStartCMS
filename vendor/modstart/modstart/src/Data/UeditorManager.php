<?php


namespace ModStart\Data;


use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Assets\AssetsUtil;
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
            // 上传图片配置项
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

            // [暂未开启] 涂鸦图片上传配置项
            "scrawlActionName" => "crawl",
            "scrawlFieldName" => "file",
            "scrawlMaxSize" => $dataUploadConfig['image']['maxSize'],
            "scrawlUrlPrefix" => "",
            "scrawlInsertAlign" => "none",

            // [暂未开启] 截图工具上传
            "snapscreenActionName" => "snap",
            "snapscreenUrlPrefix" => "",
            "snapscreenInsertAlign" => "none",

            // [暂未开启] 抓取
            "catcherLocalDomain" => ["127.0.0.1", "localhost"],
            "catcherActionName" => "catch",
            "catcherFieldName" => "source",
            "catcherUrlPrefix" => "",
            "catcherMaxSize" => $dataUploadConfig['image']['maxSize'],
            "catcherAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

            // 上传视频配置
            "videoActionName" => "video",
            "videoFieldName" => "file",
            "videoUrlPrefix" => "",
            "videoMaxSize" => $dataUploadConfig['video']['maxSize'],
            "videoAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['video']['extensions']),

            // 上传文件配置
            "fileActionName" => "file",
            "fileFieldName" => "file",
            "fileUrlPrefix" => "",
            "fileMaxSize" => $dataUploadConfig['file']['maxSize'],
            "fileAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['file']['extensions']),

            // 列出图片
            "imageManagerActionName" => "listImage",
            "imageManagerListSize" => 20,
            "imageManagerUrlPrefix" => "",
            "imageManagerInsertAlign" => "none",
            "imageManagerAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

            // 列出指定目录下的文件
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
                if ($uploadTable == 'admin_upload' && AdminPermission::isDemo()) {
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
                $ignores = array_filter([
                    trim(AssetsUtil::cdn(), '/') ? AssetsUtil::cdn() : null,
                ]);
                foreach ($list as $f) {
                    $ignoreCatch = false;
                    foreach ($ignores as $ignore) {
                        if (str_contains($f, $ignore)) {
                            $ignoreCatch = true;
                            break;
                        }
                    }
                    if (!$ignoreCatch && preg_match('/^(http|ftp|https):\\/\\//i', $f)) {
                        $ext = FileUtil::extension($f);
                        if (in_array('.' . $ext, $config ['catcherAllowFiles'])) {
                            $imageContent = CurlUtil::getRaw($f);
                            if ($imageContent) {
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
