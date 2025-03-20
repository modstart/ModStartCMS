<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Support\Concern\HasFields;
use Module\AigcBase\Util\AigcKeyPoolUtil;

abstract class AbstractAigcProvider
{
    public $defaultModel;

    abstract public function name();

    abstract public function title();

    abstract public function type();

    abstract public function functions();

    public function models()
    {
        return [
            'default' => '默认'
        ];
    }

    public function help()
    {
        return '';
    }

    /**
     * 参数表单编辑
     * @param $builder HasFields
     * @param $param array
     * @return void
     */
    public function paramForm($builder, $param = [])
    {
        $builder->complexFields('param', '参数')
            ->fields([
                //['name' => 'model', 'title' => '模型', 'type' => 'select', 'defaultValue' => '', 'tip' => '', 'option' => [
                //    'default' => 'default',
                //]],
                ['name' => 'key', 'title' => 'Key', 'type' => 'text', 'defaultValue' => '', 'tip' => '',],
                //['name' => 'appId', 'title' => 'AppId', 'type' => 'text', 'defaultValue' => '', 'tip' => '',],
                //['name' => 'appSecret', 'title' => 'AppSecret', 'type' => 'text', 'defaultValue' => '', 'tip' => '',],
                //['name' => 'appKey', 'title' => 'AppKey', 'type' => 'text', 'defaultValue' => '', 'tip' => '',],
                //['name' => 'apiKey', 'title' => 'ApiKey', 'type' => 'text', 'defaultValue' => '', 'tip' => '',],
            ])
            ->listable(false)->showable(false);
    }

    /** 参数显示
     * @param $item array
     * @param $param array
     * @return array
     * @returnExample
     * [
     *    {"name":"xxx","value":"xxx"}
     * ]
     */
    public function paramDisplay($item, $param = [])
    {
        $result = [];
        $itemParam = SerializeUtil::jsonDecode($item['param']);
        $map = [
            'model' => '模型',
            'appId' => 'AppId',
            'appSecret' => 'AppSecret',
            'key' => 'Key',
        ];
        if (!empty($itemParam)) {
            foreach ($itemParam as $k => $v) {
                if (isset($map[$k])) {
                    $k = $map[$k];
                }
                $result[] = [
                    'name' => $k,
                    'value' => $v,
                ];
            }
        }
        return $result;
    }

    protected function keyPoolGetOrFail($model = null)
    {
        if (null === $model) {
            $model = $this->defaultModel;
        }
        BizException::throwsIfEmpty('AI模型没有选择', $model);
        $keyPool = AigcKeyPoolUtil::randomByType(static::NAME, $model);
        BizException::throwsIfEmpty('AI没有配置(name=' . static::NAME . ',model=' . $model . ')', $keyPool);
        return $keyPool;
    }

    protected function keyPoolSuccess($keyPool)
    {
        AigcKeyPoolUtil::markSuccess($keyPool);
    }

    protected function keyPoolFail($keyPool)
    {
        AigcKeyPoolUtil::markFail($keyPool);
    }
}
