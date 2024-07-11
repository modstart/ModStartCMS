<?php


namespace Module\DataQcloudCosFe\Core;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Data\AbstractRemoteDataStorage;
use ModStart\Data\Event\DataFileUploadedEvent;
use Module\DataQcloudCosFe\Util\DataQcloudCosFeUtil;
use Qcloud\Cos\Client;
use QCloud\COSSTS\Sts;

class DataQcloudCosFeDataStorage extends AbstractRemoteDataStorage
{
    /**
     * @var Client
     */
    private $client;
    private $bucket;

    public function init()
    {
        DataQcloudCosFeUtil::init();
        $this->remoteType = 'DataQcloudCosFe';
        $this->client = new Client([
            'region' => $this->option['region'],
            'credentials' => [
                'secretId' => $this->option['secretId'],
                'secretKey' => $this->option['secretKey'],
            ]
        ]);
        $this->bucket = $this->option['bucket'];
    }

    public function has($file)
    {
        return $this->client->doesObjectExist($this->bucket, $file);
    }

    public function move($from, $to)
    {
        try {
            $this->client->copy($this->bucket, $to, [
                'Region' => $this->option['region'],
                'Bucket' => $this->bucket,
                'Key' => $from,
            ]);
            $this->client->DeleteObject([
                'Bucket' => $this->bucket,
                'Key' => $from,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function delete($file)
    {
        try {
            $this->client->DeleteObject([
                'Bucket' => $this->bucket,
                'Key' => $file,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function put($file, $content)
    {
        $this->client->PutObject([
            'Bucket' => $this->bucket,
            'Key' => $file,
            'Body' => $content,
        ]);
    }

    public function get($file)
    {
        return $this->client->GetObject([
            'Bucket' => $this->bucket,
            'Key' => $file,
        ]);
    }

    public function size($file)
    {
        exit('TODO');
    }

    public function getUploadConfig()
    {
        return Cache::remember('DataQcloudCosFe:Config', 30, function () {
            $sts = new Sts();
            $config = [
                // url和domain保持一致
                'url' => 'https://sts.tencentcloudapi.com/',
                // 域名，非必须，默认为 sts.tencentcloudapi.com
                'domain' => 'sts.tencentcloudapi.com',
                'proxy' => '',
                'secretId' => modstart_config('DataQcloudCosFe_SecretId'),
                'secretKey' => modstart_config('DataQcloudCosFe_SecretKey'),
                'bucket' => modstart_config('DataQcloudCosFe_Bucket'),
                'region' => modstart_config('DataQcloudCosFe_Region'),
                // 密钥有效期
                'durationSeconds' => 3600,
                // 这里改成允许的路径前缀，可以根据自己网站的用户登录态判断允许上传的具体路径，例子： a.jpg 或者 a/* 或者 * (使用通配符*存在重大安全风险, 请谨慎评估使用)
                'allowPrefix' => [
                    'data_temp/*'
                ],
                // 密钥的权限列表。简单上传和分片需要以下的权限
                //  其他权限列表请看 https://cloud.tencent.com/document/product/436/31923
                'allowActions' => [
                    // 简单上传
                    'name/cos:PutObject',
                    'name/cos:PostObject',
                    // 分片上传
                    'name/cos:InitiateMultipartUpload',
                    'name/cos:ListMultipartUploads',
                    'name/cos:ListParts',
                    'name/cos:UploadPart',
                    'name/cos:CompleteMultipartUpload'
                ],
                // 临时密钥生效条件，关于condition的详细设置规则和COS支持的condition类型可以参考 https://cloud.tencent.com/document/product/436/71306
                // "condition" => array(
                //     "ip_equal" => array(
                //         "qcs:ip" => array(
                //             "10.217.182.3/24",
                //             "111.21.33.72/24",
                //         )
                //     )
                // )
            ];
            $tempKeys = $sts->getTempKeys($config);
            $data = [];
            $data['bucket'] = $config['bucket'];
            $data['region'] = $config['region'];
            $data['TmpSecretId'] = $tempKeys['credentials']['tmpSecretId'];
            $data['TmpSecretKey'] = $tempKeys['credentials']['tmpSecretKey'];
            $data['SecurityToken'] = $tempKeys['credentials']['sessionToken'];
            $data['StartTime'] = $tempKeys['startTime'];
            $data['ExpiredTime'] = $tempKeys['expiredTime'];
            return $data;
        });
    }

    public function multiPartInit($param)
    {
        $token = $this->multiPartInitToken($param);
        $this->uploadChunkTokenAndUpdateToken($token);
        $token['uploadConfig'] = $this->getUploadConfig();
        return Response::generate(0, 'ok', $token);
    }

    public function multiPartUploadEnd($param)
    {
        $category = $param['category'];
        $token = $this->multiPartInitToken($param);
        BizException::throwsIfEmpty('DataQcloudCosFe.TokenEmpty', $token);
        DataFileUploadedEvent::fire('DataQcloudCosFe', $category, $token['fullPath'], isset($param['eventOpt']) ? $param['eventOpt'] : []);
        $dataTemp = $this->repository->addTemp($category, $token['path'], $token['name'], $token['size'], empty($token['md5']) ? null : $token['md5']);
        $data['data'] = $dataTemp;
        $data['path'] = $token['fullPath'];
        $data['preview'] = $this->getDriverFullPath($token['fullPath']);
        $data['finished'] = true;
        $this->uploadChunkTokenAndDeleteToken($token);
        return Response::generateSuccessData($data);
    }

}
