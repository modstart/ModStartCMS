<?php


namespace ModStart\Core\Provider;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;

/**
 * Class FontProvider
 * @package ModStart\Core\Provider
 * @method static AbstractFontProvider first()
 */
class FontProvider
{
    /**
     * @var array
     */
    private static $list = [
        DefaultFontProvider::class
    ];

    use ProviderTrait;


    public static function firstLocalPathOrFail()
    {
        $provider = self::first();
        BizException::throwsIfEmpty('FontProvider Empty', $provider);
        $fontLocalPath = $provider->path();
        return FileUtil::savePathToLocalTemp($fontLocalPath, 'ttf', true);
    }
}
