<?php


namespace ModStart\Field;


class Display extends AbstractField
{
    protected $addable = false;
    protected $editable = false;

    public function content($content)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($content) {
            return AutoRenderedFieldValue::make($content);
        });
        $this->addable(true);
        return $this;
    }
}
