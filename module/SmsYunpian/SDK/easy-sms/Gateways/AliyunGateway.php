<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class AliyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://dysmsapi.aliyuncs.com';

    const ENDPOINT_METHOD = 'SendSms';

    const ENDPOINT_VERSION = '2017-05-25';

    const ENDPOINT_FORMAT = 'JSON';

    const ENDPOINT_REGION_ID = 'cn-hangzhou';

    const ENDPOINT_SIGNATURE_METHOD = 'HMAC-SHA1';

    const ENDPOINT_SIGNATURE_VERSION = '1.0';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        $signName = !empty($data['sign_name']) ? $data['sign_name'] : $config->get('sign_name');

        unset($data['sign_name']);

        $params = [
            'RegionId' => self::ENDPOINT_REGION_ID,
            'AccessKeyId' => $config->get('access_key_id'),
            'Format' => self::ENDPOINT_FORMAT,
            'SignatureMethod' => self::ENDPOINT_SIGNATURE_METHOD,
            'SignatureVersion' => self::ENDPOINT_SIGNATURE_VERSION,
            'SignatureNonce' => uniqid(),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Action' => self::ENDPOINT_METHOD,
            'Version' => self::ENDPOINT_VERSION,
            'PhoneNumbers' => !\is_null($to->getIDDCode()) ? strval($to->getZeroPrefixedNumber()) : $to->getNumber(),
            'SignName' => $signName,
            'TemplateCode' => $message->getTemplate($this),
            'TemplateParam' => json_encode($data, JSON_FORCE_OBJECT),
        ];

        $params['Signature'] = $this->generateSign($params);

        $result = $this->get(self::ENDPOINT_URL, $params);

        if ('OK' != $result['Code']) {
            throw new GatewayErrorException($result['Message'], $result['Code'], $result);
        }

        return $result;
    }

    
    protected function generateSign($params)
    {
        ksort($params);
        $accessKeySecret = $this->config->get('access_key_secret');
        $stringToSign = 'GET&%2F&'.urlencode(http_build_query($params, null, '&', PHP_QUERY_RFC3986));

        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret.'&', true));
    }
}
