<?php



namespace Overtrue\EasySms\Gateways;

use GuzzleHttp\Exception\RequestException;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

class HuaweiGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_HOST = 'https://api.rtc.huaweicloud.com:10443';

    const ENDPOINT_URI = '/sms/batchSendSms/v1';

    const SUCCESS_CODE = '000000';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $appKey = $config->get('app_key');
        $appSecret = $config->get('app_secret');
        $channels = $config->get('from');
        $statusCallback = $config->get('callback', '');

        $endpoint = $this->getEndpoint($config);
        $headers = $this->getHeaders($appKey, $appSecret);

        $templateId = $message->getTemplate($this);
        $messageData = $message->getData($this);

                $from = 'default';
        if (isset($messageData['from'])) {
            $from = $messageData['from'];
            unset($messageData['from']);
        }
        $channel = isset($channels[$from]) ? $channels[$from] : '';

        if (empty($channel)) {
            throw new InvalidArgumentException("From Channel [{$from}] Not Exist");
        }

        $params = [
            'from' => $channel,
            'to' => $to->getUniversalNumber(),
            'templateId' => $templateId,
            'templateParas' => json_encode($messageData),
            'statusCallback' => $statusCallback,
        ];

        try {
            $result = $this->request('post', $endpoint, [
                'headers' => $headers,
                'form_params' => $params,
                                'verify' => false,
            ]);
        } catch (RequestException $e) {
            $result = $this->unwrapResponse($e->getResponse());
        }

        if (self::SUCCESS_CODE != $result['code']) {
            throw new GatewayErrorException($result['description'], ltrim($result['code'], 'E'), $result);
        }

        return $result;
    }

    
    protected function getEndpoint(Config $config)
    {
        $endpoint = rtrim($config->get('endpoint', self::ENDPOINT_HOST), '/');

        return $endpoint.self::ENDPOINT_URI;
    }

    
    protected function getHeaders($appKey, $appSecret)
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
            'X-WSSE' => $this->buildWsseHeader($appKey, $appSecret),
        ];
    }

    
    protected function buildWsseHeader($appKey, $appSecret)
    {
        $now = date('Y-m-d\TH:i:s\Z');
        $nonce = uniqid();
        $passwordDigest = base64_encode(hash('sha256', ($nonce.$now.$appSecret)));

        return sprintf(
            'UsernameToken Username="%s",PasswordDigest="%s",Nonce="%s",Created="%s"',
            $appKey,
            $passwordDigest,
            $nonce,
            $now
        );
    }
}
