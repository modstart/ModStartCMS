<?php

namespace Overtrue\Socialite;

use Closure;
use InvalidArgumentException;
use Overtrue\Socialite\Contracts\FactoryInterface;
use Overtrue\Socialite\Contracts\ProviderInterface;

class SocialiteManager implements FactoryInterface
{
    protected  $config;
    protected  $resolved = [];
    protected  $customCreators = [];
    protected  $providers = [
        Providers\QQ::NAME => Providers\QQ::class,
        Providers\Weibo::NAME => Providers\Weibo::class,
        Providers\WeChat::NAME => Providers\WeChat::class,
    ];

    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    
    public function config(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    
    public function create($name)
    {
        $name = strtolower($name);

        if (!isset($this->resolved[$name])) {
            $this->resolved[$name] = $this->createProvider($name);
        }

        return $this->resolved[$name];
    }

    
    public function extend($name, Closure $callback)
    {
        $this->customCreators[strtolower($name)] = $callback;

        return $this;
    }

    
    public function getResolvedProviders()
    {
        return $this->resolved;
    }

    
    public function buildProvider($provider, array $config)
    {
        return new $provider($config);
    }

    
    protected function createProvider($name)
    {
        $config = $this->config->get($name, []);
        $provider = !empty($config['provider']) ?$config['provider']: $name;

        if (isset($this->customCreators[$provider])) {
            return $this->callCustomCreator($provider, $config);
        }

        if (!$this->isValidProvider($provider)) {
            throw new InvalidArgumentException("Provider [$provider] not supported.");
        }

        return $this->buildProvider($this->providers[$provider] ?$this->providers[$provider]: $provider, $config);
    }

    
    protected function callCustomCreator($driver, array $config)
    {
        return $this->customCreators[$driver]($config);
    }

    
    protected function isValidProvider($provider)
    {
        return isset($this->providers[$provider]) || is_subclass_of($provider, ProviderInterface::class);
    }
}
