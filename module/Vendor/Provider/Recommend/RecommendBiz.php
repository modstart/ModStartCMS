<?php


namespace Module\Vendor\Provider\Recommend;

use Module\Vendor\Provider\BizTrait;

/**
 * @method static AbstractRecommendBiz[] listAll()
 * @method static AbstractRecommendBiz getByName($name)
 */
class RecommendBiz
{
    use BizTrait;

    public static function itemFeedback($biz, $bizId, $userId, $type)
    {
        $biz = self::getByName($biz);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemFeedback(
                $biz->name(),
                $bizId,
                $userId,
                $type
            );
        }
    }

    public static function itemVisit($biz, $bizId, $userId)
    {
        self::itemFeedback($biz, $bizId, $userId, RecommendUserFeedbackType::VISIT);
    }
}
