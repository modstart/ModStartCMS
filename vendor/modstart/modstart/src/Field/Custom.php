<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;

/**
 * Class Custom
 * @package ModStart\Field
 * @since 1.6.0
 */
class Custom extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'customRenderView' => '',
            'customRenderFormView' => '',
            'customRenderGridView' => '',
            'customRenderDetailView' => '',
        ]);
    }

    public function customRenderView($view)
    {
        $this->addVariables([
            'customRenderView' => $view,
        ]);
        return $this;
    }

    public function customRenderFormView($view)
    {
        $this->addVariables([
            'customRenderFormView' => $view,
        ]);
        return $this;
    }

    public function customRenderGridView($view)
    {
        $this->addVariables([
            'customRenderGridView' => $view,
        ]);
        return $this;
    }

    public function customRenderDetailView($view)
    {
        $this->addVariables([
            'customRenderDetailView' => $view,
        ]);
        return $this;
    }

    public function renderView(AbstractField $field, $item, $index = 0)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) {
            $view = $this->getVariable('customRender' . ucfirst($this->renderMode()) . 'View');
            if (empty($view)) {
                $view = $this->getVariable('customRenderView');
            }
            if (empty($view)) {
                return null;
            }
            $variables = $this->variables();
            return AutoRenderedFieldValue::makeView($view, $variables);
        });
        return parent::renderView($field, $item, $index);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        return @json_decode($value, true);
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $json = @json_decode($value, true);
        BizException::throwsIf($this->label . ' ' . L('Json Format Error'), $value && null === $json);
        return $json;
    }
}
