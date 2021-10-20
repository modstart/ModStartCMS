<?php



namespace Overtrue\EasySms\Strategies;

use Overtrue\EasySms\Contracts\StrategyInterface;


class RandomStrategy implements StrategyInterface
{
    
    public function apply(array $gateways)
    {
        uasort($gateways, function () {
            return mt_rand() - mt_rand();
        });

        return array_keys($gateways);
    }
}
