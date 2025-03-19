<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use Module\AigcBase\Type\AigcProviderType;

abstract class AbstractAigcVideoProvider extends AbstractAigcProvider
{
    public function type()
    {
        return AigcProviderType::VIDEO;
    }

    /**
     * @param $stage string AigcProviderStage
     * @param $stageData array
     * @param $modelConfigOrResult array
     * @param $option array
     * @return array
     * @throws BizException
     * @example for queue
     *    [
     *     'code' => 0,
     *     'msg' => '',
     *     'data'=>[
     *         'result'=>[ 'foo'=>'bar',]
     *      ]
     *    ]
     * @example for query
     *    [
     *     'code' => 0,
     *     'msg' => '',
     *     'data'=>[
     *         'status'=>'SUCCESS|FAIL|RUNNING',
     *         'result'=>[ 'foo'=>'bar',]
     *      ]
     *    ]
     */
    public function videoGen($stage, $stageData, $modelConfigOrResult, $option = [])
    {
        BizException::throws('未实现方法 AbstractAigcSoundProvider.soundTts');
    }
}
