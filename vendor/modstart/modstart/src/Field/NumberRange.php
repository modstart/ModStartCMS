<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;

class NumberRange extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            // 自动着色，正数绿色，负数红色
            'autoColor' => false,
            // 是否显示符号
            'signShow' => false,
            // 单位
            'unit' => null,
            // 最小值
            'min' => null,
            // 最大值
            'max' => null,
            // stepping interval
            'step' => null,
        ]);
    }

    public function min($value)
    {
        $this->setVariable('min', $value);
        return $this;
    }

    public function max($value)
    {
        $this->setVariable('max', $value);
        return $this;
    }

    public function step($value)
    {
        $this->setVariable('step', $value);
        return $this;
    }

    public function autoColor($enable)
    {
        $this->setVariable('autoColor', $enable);
        return $this;
    }

    public function signShow($enable)
    {
        $this->setVariable('signShow', $enable);
        return $this;
    }

    public function unit($value)
    {
        $this->setVariable('unit', $value);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [
                'min' => '',
                'max' => '',
            ];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [
                'min' => '',
                'max' => '',
            ];
        }
        if (
            ('' === $value['min'] || null === $value['min'])
            &&
            ('' === $value['max'] || null === $value['max'])
        ) {
            return null;
        }
        $value['min'] = intval($value['min']);
        $value['max'] = intval($value['max']);
        if ($value['min'] > $value['max']) {
            BizException::throws('最小值不能大于最大值');
        }
        foreach (['min', 'max'] as $k) {
            if (null !== ($v = $this->getVariable('min'))) {
                if ($value[$k] < $v) {
                    BizException::throws(str_replace([
                        ':attribute', ':min'
                    ], [
                        $this->label, $v
                    ], L('validation.min.numeric')));
                }
            }
            if (null !== ($v = $this->getVariable('max'))) {
                if ($value[$k] > $v) {
                    BizException::throws(str_replace([
                        ':attribute', ':max'
                    ], [
                        $this->label, $v
                    ], L('validation.max.numeric')));
                }
            }
        }
        return $value;
    }
}
