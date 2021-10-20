<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class YunzhixunGateway extends Gateway
{
    use HasHttpRequest;

    const SUCCESS_CODE = '000000';

    const FUNCTION_SEND_SMS = 'sendsms';

    const FUNCTION_BATCH_SEND_SMS = 'sendsms_batch';

    const ENDPOINT_TEMPLATE = 'https://open.ucpaas.com/ol/%s/%s';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        $function = isset($data['mobiles']) ? self::FUNCTION_BATCH_SEND_SMS : self::FUNCTION_SEND_SMS;

        $endpoint = $this->buildEndpoint('sms', $function);

        $params = $this->buildParams($to, $message, $config);

        return $this->execute($endpoint, $params);
    }

    
    protected function buildEndpoint($resource, $function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $resource, $function);
    }

    
    protected function buildParams(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        return [
            'sid' => $config->get('sid'),
            'token' => $config->get('token'),
            'appid' => $config->get('app_id'),
            'templateid' => $message->getTemplate($this),
            'uid' => isset($data['uid']) ? $data['uid'] : '',
            'param' => isset($data['params']) ? $data['params'] : '',
            'mobile' => isset($data['mobiles']) ? $data['mobiles'] : $to->getNumber(),
        ];
    }

    
    protected function execute($endpoint, $params)
    {
        try {
            $result = $this->postJson($endpoint, $params);

            if (!isset($result['code']) || self::SUCCESS_CODE !== $result['code']) {
                $code = isset($result['code']) ? $result['code'] : 0;
                $error = isset($result['msg']) ? $result['msg'] : json_encode($result, JSON_UNESCAPED_UNICODE);

                throw new GatewayErrorException($error, $code);
            }

            return $result;
        } catch (\Exception $e) {
            throw new GatewayErrorException($e->getMessage(), $e->getCode());
        }
    }
}
