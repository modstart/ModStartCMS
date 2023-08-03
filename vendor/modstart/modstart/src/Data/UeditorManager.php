<?php


namespace ModStart\Data;


use Illuminate\Support\Facades\Input;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Data\Event\DataUploadedEvent;
use ModStart\Data\Event\DataUploadingEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UeditorManager
{
    private static function listCatcherIgnoreDomains()
    {
        $list = [];
        if ($cdn = (trim(AssetsUtil::cdn(), '/') ? AssetsUtil::cdn() : null)) {
            $list[] = $cdn;
        }
        if ($domain = Request::domain()) {
            $list[] = $domain;
        }
        $storage = DataManager::storage();
        if ($storage) {
            if ($d = $storage->domain()) {
                $list[] = $d;
            }
        }
        $domains = modstart_config()->getArray('Data_RemoteCatchIgnoreDomains', []);
        if (!empty($domains)) {
            $list = array_merge($list, $domains);
        }
        return array_unique($list);
    }

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
            "catcherLocalDomain" => self::listCatcherIgnoreDomains(),
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
            }, $dataUploadConfig['file']['extensions']),

            // 公式渲染
            "formulaConfig" => [
                "imageUrlTemplate" => modstart_config('UEditor_FormulaImageUrlTemplate', 'https://latex.codecogs.com/svg.image?{}'),
            ]
        ];
        return $config;
    }

    private static function saveToUser($uploadTable, $userId, $data)
    {
        ModelUtil::insert($uploadTable, [
            'category' => $data['category'],
            'dataId' => $data['id'],
            'uploadCategoryId' => 0,
            'userId' => $userId,
        ]);
    }

    private static function resultError($result = null, $error = 'ERROR')
    {
        if (null == $result) {
            $result = [
                'state' => '',
            ];
        }
        $result['state'] = $error;
        return Response::jsonRaw($result);
    }

    public static function handle($uploadTable, $uploadCategoryTable, $userId, $option = null)
    {
        $config = self::basicConfig();
        $input = InputPackage::buildFromInput();
        $action = $input->getTrimString('action');
        if (in_array($action, ['image', 'catch'])) {
            set_time_limit(60);
            if ($uploadTable == 'admin_upload' && AdminPermission::isDemo()) {
                return self::resultError();
            }
        }
        switch ($action) {
            case 'config':
                return Response::jsonRaw($config);
            case 'image':
                DataUploadingEvent::fire($uploadTable, $userId, 'image');
                $editorRet = [
                    'state' => 'SUCCESS',
                    'url' => null
                ];
                /** @var UploadedFile $file */
                $file = Input::file('file');
                if (empty($file)) {
                    return self::resultError($editorRet, 'File Empty');
                }
                $filename = FileUtil::getUploadFileNameWithExt($file);
                $content = file_get_contents($file->getRealPath());
                $ret = DataManager::upload('image', $filename, $content, $option);
                if ($ret['code']) {
                    return self::resultError($editorRet, $ret['msg']);
                }
                self::saveToUser($uploadTable, $userId, $ret['data']['data']);
                $editorRet['url'] = $ret['data']['fullPath'];
                DataUploadedEvent::fire($uploadTable, $userId, 'image', $ret['data']['data']['id']);
                return Response::jsonRaw($editorRet);
            case 'catch':
                DataUploadingEvent::fire($uploadTable, $userId, 'image');
                $editorRet = [
                    'state' => '',
                    'list' => null
                ];
                $saveList = [];
                $list = $input->getArray($config['catcherFieldName']);
                if (empty($list)) {
                    return self::resultError($editorRet);
                }
                $editorRet ['state'] = 'SUCCESS';
                $ignores = self::listCatcherIgnoreDomains();
                foreach ($list as $f) {
                    $ignoreCatch = false;
                    foreach ($ignores as $ignore) {
                        if (str_contains($f, $ignore)) {
                            $ignoreCatch = true;
                            break;
                        }
                    }
                    if (!$ignoreCatch && preg_match('/^(http|ftp|https):\\/\\//i', $f)) {
                        $ext = null;
                        $urlInfo = parse_url($f);
                        if (!empty($urlInfo['path'])) {
                            $ext = FileUtil::extension($urlInfo['path']);
                        }
                        if (in_array('.' . $ext, $config ['catcherAllowFiles'])) {
                            $imageContent = CurlUtil::getRaw($f);
                            if ($imageContent) {
                                $ret = DataManager::upload('image', L('Image') . '.' . $ext, $imageContent, $option);
                                if ($ret['code']) {
                                    $ret['state'] = $ret['msg'];
                                } else {
                                    self::saveToUser($uploadTable, $userId, $ret['data']['data']);
                                    DataUploadedEvent::fire($uploadTable, $userId, 'image', $ret['data']['data']['id']);
                                    $saveList [] = [
                                        'state' => 'SUCCESS',
                                        'url' => $ret['data']['fullPath'],
                                        'size' => strlen($imageContent),
                                        'title' => '',
                                        'original' => '',
                                        'source' => htmlspecialchars($f)
                                    ];
                                }
                            } else {
                                $ret ['state'] = 'Get remote file error';
                            }
                        } else {
                            $ret ['state'] = 'File ext not allowed';
                        }
                    } else {
                        $saveList [] = array(
                            'state' => 'not remote image',
                            'url' => '',
                            'size' => '',
                            'title' => '',
                            'original' => '',
                            'source' => htmlspecialchars($f)
                        );
                    }
                }
                $editorRet ['list'] = $saveList;
                return Response::jsonRaw($editorRet);
        }
    }
}
