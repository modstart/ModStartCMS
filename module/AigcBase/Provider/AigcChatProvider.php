<?php


namespace Module\AigcBase\Provider;

use Module\AigcBase\Type\AigcProviderType;

/**
 * Class ChatAiProvider
 *
 * @method static AbstractAigcChatProvider getByName($name)
 */
class AigcChatProvider extends AigcProvider
{
    /**
     * @return AbstractAigcChatProvider[]
     */
    public static function listAll()
    {
        return AigcProvider::listAllByTypeFunction(AigcProviderType::CHAT);
    }

    /**
     * @return array
     */
    public static function allMap()
    {
        return AigcProvider::allMapByTypeFunction(AigcProviderType::CHAT);
    }

    /**
     * @return array
     */
    public static function modelMap($filters = null)
    {
        $map = AigcProvider::modelsMapByTypeFunction(AigcProviderType::CHAT);
        if (null === $filters) {
            return $map;
        }
        return array_filter($map, function ($k) use ($filters) {
            return in_array($k, $filters);
        }, ARRAY_FILTER_USE_KEY);
    }

}
