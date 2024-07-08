<?php


namespace Module\DataAliyunOssFe\Core;

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleRequest;
use AlibabaCloud\SDK\Sts\V20150401\Sts;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;
use Illuminate\Support\Facades\Cache;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PathUtil;
use ModStart\Data\AbstractDataStorage;
use ModStart\Data\Event\DataFileUploadedEvent;
use Module\DataAliyunOssFe\Util\DataAliyunOssFeUtil;
use OSS\Core\OssException;
use OSS\OssClient;

class DataAliyunOssFeDataStorage extends AbstractDataStorage
{
    /**
     * @var OssClient
     */
    private $client;
    private $bucket;

    public function init()
    {
        DataAliyunOssFeUtil::init();
        $this->client = new OssClient(
            $this->option['accessKeyId'],
            $this->option['accessKeySecret'],
            $this->option['endpoint']
        );
        $this->bucket = $this->option['bucket'];
    }

    public function driverName()
    {
        return 'AliyunOss';
    }


    public function has($file)
    {
        try {
            $ret = $this->client->getObjectMeta($this->bucket, $file);
            if (empty($ret['etag'])) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function move($from, $to)
    {
        try {
            $this->client->copyObject($this->bucket, $from, $this->bucket, $to);
        } catch (OssException $e) {
            if (str_contains($e->getMessage(), 'NoSuchKey')) {
            } else {
                throw $e;
            }
        }
        try {
            $this->client->deleteObject($this->bucket, $from);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'NoSuchKey')) {
            } else {
                throw $e;
            }
        }
    }

    public function delete($file)
    {
        try {
            $ret = $this->client->deleteObject($this->bucket, $file);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'NoSuchKey')) {
            } else {
                throw $e;
            }
        }
    }

    public function put($file, $content)
    {
        $this->client->putObject($this->bucket, $file, $content);
    }

    public function get($file)
    {
        return $this->client->getObject($this->bucket, $file);
    }

    public function size($file)
    {
        try {
            $ret = $this->client->getObjectMeta($this->bucket, $file);
            return intval($ret['content-length']);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getUploadConfig()
    {
        return Cache::remember('DataAliyunOssFe:Config', 30, function () {
            DataAliyunOssFeUtil::init();
            $bucket = modstart_config('DataAliyunOssFe_Bucket');
            $config = new Config([
                "accessKeyId" => modstart_config('DataAliyunOssFe_Front_AccessKeyId'),
                "accessKeySecret" => modstart_config('DataAliyunOssFe_Front_AccessKeySecret'),
            ]);

            $config->endpoint = modstart_config('DataAliyunOssFe_Front_StsEndpoint');
            $client = new Sts($config);

            $assumeRoleRequest = new AssumeRoleRequest([
                // roleArn填写步骤2获取的角色ARN
                "roleArn" => modstart_config('DataAliyunOssFe_Front_RoleArn'),
                // roleSessionName用于自定义角色会话名称，用来区分不同的令牌，例如填写为sessiontest。
                "roleSessionName" => "upload-to-data-temp",
                // durationSeconds用于设置临时访问凭证有效时间单位为秒，最小值为900，最大值以当前角色设定的最大会话时间为准。本示例指定有效时间为3000秒。
                "durationSeconds" => 3600,
                // policy填写自定义权限策略，用于进一步限制STS临时访问凭证的权限。
                // 如果不指定Policy，则返回的STS临时访问凭证默认拥有指定角色的所有权限。
                // 临时访问凭证最后获得的权限是步骤4设置的角色权限和该Policy设置权限的交集。
                "policy" => '{
    "Version": "1",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": "oss:PutObject",
            "Resource": "acs:oss:*:*:' . $bucket . '/data_temp/*"
        }
    ]
}',
            ]);
            $runtime = new RuntimeOptions([]);
            $result = $client->assumeRoleWithOptions($assumeRoleRequest, $runtime);
            return [
                'region' => 'oss-' . modstart_config('DataAliyunOssFe_Region'),
                'bucket' => $bucket,
                'accessKeyId' => $result->body->credentials->accessKeyId,
                'accessKeySecret' => $result->body->credentials->accessKeySecret,
                'securityToken' => $result->body->credentials->securityToken,
                'expiration' => $result->body->credentials->expiration,
            ];
        });
    }

    public function multiPartInit($param)
    {
        $token = $this->multiPartInitToken($param);
        $this->uploadChunkTokenAndUpdateToken($token);
        $token['uploadConfig'] = $this->getUploadConfig();
        return Response::generateSuccessData($token);
    }

    public function multiPartUploadEnd($param)
    {
        $category = $param['category'];
        $token = $this->multiPartInitToken($param);
        BizException::throwsIfEmpty('DataAliyunOssFe.TokenEmpty', $token);
        DataFileUploadedEvent::fire('AliyunOss', $category, $token['fullPath'], isset($param['eventOpt']) ? $param['eventOpt'] : []);
        $dataTemp = $this->repository->addTemp($category, $token['path'], $token['name'], $token['size'], empty($token['md5']) ? null : $token['md5']);
        $data['data'] = $dataTemp;
        $data['path'] = $token['fullPath'];
        $data['preview'] = $this->getDriverFullPath($token['fullPath']);
        $data['finished'] = true;
        $this->uploadChunkTokenAndDeleteToken($token);
        return Response::generateSuccessData($data);
    }

    public function domain()
    {
        return modstart_config()->getWithEnv('DataAliyunOssFe_Domain');
    }

    public function domainInternal()
    {
        return modstart_config()->getWithEnv('DataAliyunOssFe_DomainInternal', modstart_config()->getWithEnv('DataAliyunOssFe_Domain'));
    }


    public function updateDriverDomain($data)
    {
        $update = [
            'driver' => 'AliyunOss',
            'domain' => $this->domain(),
        ];
        $this->repository->updateData($data['id'], $update);
        return array_merge($data, $update);
    }

    public function getDriverFullPath($path)
    {
        $path = parent::getDriverFullPath($path);
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return $this->domain() . $path;
    }

    /**
     * @param $path
     * @return mixed|string
     * @deprecated delete at 2024-04-25
     */
    public function getDriverFullPathInternal($path)
    {
        $path = parent::getDriverFullPathInternal($path);
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return $this->domainInternal() . $path;
    }


}
