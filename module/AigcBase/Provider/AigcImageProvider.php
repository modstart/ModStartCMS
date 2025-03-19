<?php


namespace Module\AigcBase\Provider;

/**
 * Class ChatAiProvider
 *
 * @method static AbstractAigcVideoProvider[] listAll()
 * @method static AbstractAigcVideoProvider getByName($name)
 */
class AigcImageProvider extends AigcProvider
{
    /**
     * @param $function
     * @return AbstractAigcVideoProvider[]
     */
    public static function listAllWithFunctions($function)
    {
        $providers = self::listAll();
        $result = [];
        foreach ($providers as $provider) {
            if (in_array($function, $provider->functions())) {
                $result[] = $provider;
            }
        }
        return $result;
    }
}
