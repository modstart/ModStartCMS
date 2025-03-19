<?php


namespace Module\AigcBase\Provider;


use Module\AigcBase\Type\AigcProviderType;

abstract class AbstractAigcImageProvider extends AbstractAigcProvider
{
    public function type()
    {
        return AigcProviderType::IMAGE;
    }

    public function functions()
    {
        return [
            //'soundTts' => '语音合成',
        ];
    }

    //public function callSoundTts()
    //{
    //
    //}

}
