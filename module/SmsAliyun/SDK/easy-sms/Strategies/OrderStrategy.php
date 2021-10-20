<?php



namespace Overtrue\EasySms\Strategies;

use Overtrue\EasySms\Contracts\StrategyInterface;


class OrderStrategy implements StrategyInterface
{
    
    public function apply(array $gateways)
    {
        return array_keys($gateways);
    }
}
