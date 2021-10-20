<?php



namespace Overtrue\EasySms\Contracts;


interface StrategyInterface
{
    
    public function apply(array $gateways);
}
