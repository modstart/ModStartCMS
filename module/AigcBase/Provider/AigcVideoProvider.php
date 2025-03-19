<?php


namespace Module\AigcBase\Provider;

use Module\AigcBase\Type\AigcProviderType;

/**
 * Class ChatAiProvider
 *
 * @method static AbstractAigcVideoProvider[] listAll()
 * @method static AbstractAigcVideoProvider getByName($name)
 */
class AigcVideoProvider extends AigcProvider
{
    const FUNCTION_VIDEO_GEN = 'videoGen';

    /**
     * @param $function
     * @return AbstractAigcVideoProvider[]
     */
    public static function listAllByFunction($function)
    {
        return AigcProvider::listAllByTypeFunction(AigcProviderType::VIDEO, $function);
    }

    /**
     * @param $function
     * @return array
     */
    public static function allMapByFunction($function)
    {
        return AigcProvider::allMapByTypeFunction(AigcProviderType::VIDEO, $function);
    }
}
