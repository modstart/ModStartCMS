<?php



namespace Overtrue\EasySms;

use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Contracts\MessageInterface;


class Message implements MessageInterface
{
    
    protected $gateways = [];

    
    protected $type;

    
    protected $content;

    
    protected $template;

    
    protected $data = [];

    
    public function __construct(array $attributes = [], $type = MessageInterface::TEXT_MESSAGE)
    {
        $this->type = $type;

        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    
    public function getMessageType()
    {
        return $this->type;
    }

    
    public function getContent(GatewayInterface $gateway = null)
    {
        return is_callable($this->content) ? call_user_func($this->content, $gateway) : $this->content;
    }

    
    public function getTemplate(GatewayInterface $gateway = null)
    {
        return is_callable($this->template) ? call_user_func($this->template, $gateway) : $this->template;
    }

    
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    
    public function getData(GatewayInterface $gateway = null)
    {
        return is_callable($this->data) ? call_user_func($this->data, $gateway) : $this->data;
    }

    
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    
    public function getGateways()
    {
        return $this->gateways;
    }

    
    public function setGateways(array $gateways)
    {
        $this->gateways = $gateways;

        return $this;
    }

    
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
