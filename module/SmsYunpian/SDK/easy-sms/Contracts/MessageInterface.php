<?php



namespace Overtrue\EasySms\Contracts;


interface MessageInterface
{
    const TEXT_MESSAGE = 'text';

    const VOICE_MESSAGE = 'voice';

    
    public function getMessageType();

    
    public function getContent(GatewayInterface $gateway = null);

    
    public function getTemplate(GatewayInterface $gateway = null);

    
    public function getData(GatewayInterface $gateway = null);

    
    public function getGateways();
}
