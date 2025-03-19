<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use Module\AigcBase\Type\AigcProviderType;

abstract class AbstractAigcSoundProvider extends AbstractAigcProvider
{

    public function type()
    {
        return AigcProviderType::SOUND;
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
    public function soundTts($stage, $stageData, $modelConfigOrResult, $option = [])
    {
        BizException::throws('未实现方法 AbstractAigcSoundProvider.soundTts');
    }

    /**
     * @param $option
     * @return array[]
     */
    public function soundTtsSpeakers($option = [])
    {
        //return [
        //    //['name' => 'xiaoyan', 'title' => '小燕', 'tags' => ['新闻联播', '小说播报'], 'preview' => 'https://www.example.com/preview.wav'],
        //    //['name' => 'xiaoyan', 'title' => '小燕', 'tags' => ['新闻联播', '小说播报'], 'preview' => 'https://www.example.com/preview.wav'],
        //];
        BizException::throws('未实现方法 AbstractAigcSoundProvider.soundTtsSpeakers');
    }

    /**
     * @param $stage string AigcProviderStage
     * @param $stageData array
     * @param $modelConfigOrResult array
     * @param $option array
     * @return array
     * @throws BizException
     * @example for queue
     *     [
     *      'code' => 0,
     *      'msg' => '',
     *      'data'=>[
     *          'result'=>[ 'foo'=>'bar',]
     *       ]
     *     ]
     * @example for query
     *     [
     *      'code' => 0,
     *      'msg' => '',
     *      'data'=>[
     *          'status'=>'SUCCESS|FAIL|RUNNING',
     *          'result'=>[ 'foo'=>'bar',]
     *       ]
     *     ]
     */
    public function soundClone($stage, $stageData, $modelConfigOrResult, $option = [])
    {
        BizException::throws('未实现方法 AbstractAigcSoundProvider.soundClone');
    }

}
