<?php



namespace Overtrue\EasySms\Contracts;

use Overtrue\EasySms\Support\Config;


interface GatewayInterface
{
    
    public function getName();

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config);
}
