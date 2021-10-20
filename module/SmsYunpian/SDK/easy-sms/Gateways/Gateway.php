<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Support\Config;


abstract class Gateway implements GatewayInterface
{
    const DEFAULT_TIMEOUT = 5.0;

    
    protected $config;

    
    protected $options;

    
    protected $timeout;

    
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    
    public function getTimeout()
    {
        return $this->timeout ?: $this->config->get('timeout', self::DEFAULT_TIMEOUT);
    }

    
    public function setTimeout($timeout)
    {
        $this->timeout = floatval($timeout);

        return $this;
    }

    
    public function getConfig()
    {
        return $this->config;
    }

    
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    
    public function setGuzzleOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    
    public function getGuzzleOptions()
    {
        return $this->options ?: $this->config->get('options', []);
    }

    
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }
}
