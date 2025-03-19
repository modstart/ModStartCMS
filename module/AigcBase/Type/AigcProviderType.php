<?php

namespace Module\AigcBase\Type;

use ModStart\Core\Type\BaseType;

class AigcProviderType implements BaseType
{
    const CHAT = 'chat';
    const IMAGE = 'image';
    const SOUND = 'sound';
    const VIDEO = 'video';

    public static function getList()
    {
        return [
            self::CHAT => '对话',
            self::IMAGE => '图片',
            self::SOUND => '声音',
            self::VIDEO => '视频',
        ];
    }
}
