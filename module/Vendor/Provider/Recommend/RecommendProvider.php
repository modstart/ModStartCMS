<?php


namespace Module\Vendor\Provider\Recommend;

use Module\Vendor\Provider\ProviderTrait;

/**
 * @method static AbstractRecommendProvider[] listAll()
 * @method static AbstractRecommendProvider getByName($name)
 */
class RecommendProvider
{
    use ProviderTrait;

    public static function randomItemBizIds($biz, $userId, $limit = 1, $sceneIds = [], $tags = [], $exceptBizIds = [], $param = [])
    {
        $biz = RecommendBiz::getByName($biz);
        if (!$biz) {
            return [];
        }
        $provider = self::getByName($biz->providerName());
        if (!$provider) {
            return [];
        }
        $ret = $provider->randomItem(
            $biz->name(),
            $userId,
            $limit,
            $sceneIds,
            $tags,
            $exceptBizIds,
            $param
        );
        if(isset($ret['data']['bizIds'])){
            return $ret['data']['bizIds'];
        }
        return [];
    }
}
