<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class YunpianGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s.yunpian.com/%s/%s/%s.%s';

    const ENDPOINT_VERSION = 'v2';

    const ENDPOINT_FORMAT = 'json';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $template = $message->getTemplate($this);
        $function = 'single_send';
        $option = [
            'form_params' => [
                'apikey' => $config->get('api_key'),
                'mobile' => $to->getUniversalNumber()
            ],
            'exceptions' => false,
        ];

        if(!is_null($template)){
            $function = 'tpl_single_send';
            $data = [];

            foreach ($message->getData($this) ?? [] as $key => $value) {
                $data[] = urlencode('#'.$key.'#') . '=' . urlencode($value);
            }

            $option['form_params'] = array_merge($option['form_params'],[
                'tpl_id' => $template,
                'tpl_value' => implode('&', $data)
            ]);
        }else{
            $content = $message->getContent($this);
            $signature = $config->get('signature', '');
            $option['form_params'] = array_merge($option['form_params'],[
                'text' => 0 === \stripos($content, '【') ? $content : $signature.$content
            ]);
        }

        $endpoint = $this->buildEndpoint('sms', 'sms', $function);
        $result = $this->request('post', $endpoint, $option);

        if ($result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }

    
    protected function buildEndpoint($type, $resource, $function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $type, self::ENDPOINT_VERSION, $resource, $function, self::ENDPOINT_FORMAT);
    }
}
