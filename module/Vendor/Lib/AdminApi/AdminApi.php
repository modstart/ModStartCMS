<?php


namespace Module\Vendor\Lib\AdminApi;


use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;

class AdminApi
{
    private $base;
    private $apiKey;

    /**
     * @param $base string 基础URL
     * @param $apiKey string API Key
     * @return AdminApi
     */
    public static function build($base = null, $apiKey = null)
    {
        if (null === $base) {
            $base = modstart_config('AdminApiRequest_Base', '');
        }
        if (null === $apiKey) {
            $apiKey = modstart_config('AdminApiRequest_ApiKey', '');
        }
        $ins = new static();
        $ins->base = $base;
        $ins->apiKey = $apiKey;
        return $ins;
    }

    public function ping()
    {
        return $this->request('admin_api/ping', []);
    }

    public function uploadImage($filename, $content)
    {
        return $this->request('admin_api/upload/image', [
            'filename' => $filename,
            'base64Content' => base64_encode($content),
        ]);
    }

    public function uploadImageTemp($filename, $content)
    {
        return $this->request('admin_api/upload/image_temp', [
            'filename' => $filename,
            'base64Content' => base64_encode($content),
        ]);
    }

    public function uploadFile($filename, $content)
    {
        return $this->request('admin_api/upload/file', [
            'filename' => $filename,
            'base64Content' => base64_encode($content),
        ]);
    }

    public function uploadFileTemp($filename, $content)
    {
        return $this->request('admin_api/upload/file_temp', [
            'filename' => $filename,
            'base64Content' => base64_encode($content),
        ]);
    }

    public function request($url, $data)
    {
        $url = rtrim($this->base, '/') . '/' . ltrim($url, '/');
        $ret = CurlUtil::request(
            $url,
            $data,
            [
                'method' => 'post',
                'header' => [
                    'admin-api-key' => $this->apiKey
                ]
            ]
        );
        if (200 == $ret['code']) {
            $result = @json_decode($ret['body'], true);
            if (empty($result)) {
                return Response::generate(0, '接口返回数据格式错误', [
                    'body' => $ret['body']
                ]);
            }
            return Response::generateSuccessData($result);
        }
        return Response::generate(0, '接口请求失败', [
            'code' => $ret['code'],
            'body' => $ret['body']
        ]);
    }
}
