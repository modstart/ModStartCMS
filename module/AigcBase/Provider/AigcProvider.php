<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use Module\Vendor\Provider\ProviderTrait;

/**
 * Class ChatAiProvider
 *
 * @method static AbstractAigcProvider[] listAll()
 * @method static AbstractAigcProvider getByName($name)
 * @method static array modelMap($filters = null)
 */
class AigcProvider
{
    use ProviderTrait;

    /**
     * @param $providerModel
     * @return AbstractAigcProvider|AbstractAigcChatProvider|AbstractAigcSoundProvider|AbstractAigcVideoProvider
     */
    public static function getByFullName($providerModel)
    {
        list($providerName, $modelName) = self::parseProviderModel($providerModel);
        $provider = self::getByName($providerName);
        $provider->defaultModel = $modelName;
        return $provider;
    }

    public static function parseProviderModel($providerModel)
    {
        $pcs = explode(':', $providerModel);
        BizException::throwsIf('模型格式错误', count($pcs) !== 2);
        return [$pcs[0], $pcs[1]];
    }

    /**
     * @param $type
     * @param $function
     * @return AbstractAigcProvider[]|array
     */
    public static function listAllByTypeFunction($type, $function = null)
    {
        $providers = self::listAll();
        $result = [];
        foreach ($providers as $provider) {
            if ($provider->type() !== $type) {
                continue;
            }
            if (null !== $function && !in_array($function, $provider->functions())) {
                continue;
            }
            $result[] = $provider;
        }
        return $result;
    }

    public static function allMapByTypeFunction($type, $function = null)
    {
        $providers = self::listAllByTypeFunction($type, $function);
        $result = [];
        foreach ($providers as $provider) {
            $result[$provider->name()] = $provider->title();
        }
        return $result;
    }

    public static function modelsMapByTypeFunction($type, $function = null)
    {
        $providers = self::listAllByTypeFunction($type, $function);
        $modelMap = [];
        foreach ($providers as $provider) {
            foreach ($provider->models() as $k => $v) {
                $name = $provider->name() . ':' . $k;
                if ($provider->name() == 'tecmz') {
                    $value = $v;
                } else {
                    $value = $provider->title() . '-' . $v;
                }
                $modelMap[$name] = $value;
            }
        }
        return $modelMap;
    }

    public static function modelsInOrder($filters = null)
    {
        $modelMap = static::modelMap($filters);
        $models = [];
        if (null === $filters) {
            foreach ($modelMap as $k => $v) {
                $models[] = ['name' => $k, 'title' => $v];
            }
            return $models;
        }
        foreach ($filters as $filter) {
            if (isset($modelMap[$filter])) {
                $models[] = ['name' => $filter, 'title' => $modelMap[$filter]];
            }
        }
        return $models;
    }
}
