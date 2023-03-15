<?php


namespace Module\Vendor\Provider\RandomImage;


use ModStart\Core\Assets\AssetsUtil;

class DefaultRandomImageProvider extends AbstractRandomImageProvider
{
    public function get($param = [])
    {
        return [
            'url' => AssetsUtil::fix('asset/image/none.svg'),
            'width' => 400,
            'height' => 400,
        ];
    }
}
