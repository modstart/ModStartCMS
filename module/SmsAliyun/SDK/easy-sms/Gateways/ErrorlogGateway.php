<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Support\Config;


class ErrorlogGateway extends Gateway
{
    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $message = sprintf(
            "[%s] to: %s | message: \"%s\"  | template: \"%s\" | data: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $message->getContent($this),
            $message->getTemplate($this),
            json_encode($message->getData($this))
        );

        $file = $this->config->get('file', ini_get('error_log'));
        $status = error_log($message, 3, $file);

        return compact('status', 'file');
    }
}
