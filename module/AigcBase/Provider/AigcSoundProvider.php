<?php


namespace Module\AigcBase\Provider;

use Module\AigcBase\Type\AigcProviderType;

/**
 * Class ChatAiProvider
 *
 * @method static AbstractAigcSoundProvider[] listAll()
 * @method static AbstractAigcSoundProvider getByName($name)
 */
class AigcSoundProvider extends AigcProvider
{
    const FUNCTION_SOUND_TTS = 'soundTts';
    const FUNCTION_SOUND_CLONE = 'soundClone';

    /**
     * @param $function
     * @return AbstractAigcSoundProvider[]
     */
    public static function listAllByFunction($function)
    {
        return AigcProvider::listAllByTypeFunction(AigcProviderType::SOUND, $function);
    }

    /**
     * @param $function
     * @return array
     */
    public static function allMapByFunction($function)
    {
        return AigcProvider::allMapByTypeFunction(AigcProviderType::SOUND, $function);
    }
}
