<?php


namespace ModStart\Grid\Concerns;


use ModStart\Field\AbstractField;
use ModStart\Field\Display;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Support\Concern\HasSetting;

/**
 * Trait HasItemOperate
 * @package ModStart\Grid\Concerns
 */
trait HasItemOperate
{
    /**
     * @var ItemOperate
     */
    private $itemOperate;

    /**
     * @var \Closure
     */
    private $hookItemOperateRendering;

    private function setupItemOperate()
    {
        $this->itemOperate = new ItemOperate($this);
    }

    private function prepareItemOperateField()
    {
        if ($this->canEdit || $this->canShow || $this->canDelete || $this->hookItemOperateRendering) {
            $field = new Display('_operate', [L('Operate')]);
            $field->hookRendering(function (AbstractField $field, $item, $index) {
                $this->itemOperate->reset()->item($item)->index($index);
                $this->itemOperate->setField($field);
                if ($this->hookItemOperateRendering) {
                    call_user_func($this->hookItemOperateRendering, $this->itemOperate);
                }
                return $this->itemOperate->render();
            });
            $this->pushField($field);
        }
    }

    public function disableItemOperate()
    {
        $this->canEdit = false;
        $this->canShow = false;
        $this->canDelete = false;
        $this->hookItemOperateRendering = false;
        return $this;
    }

    /**
     * @param null $callback = function(ItemOperate $itemOperate){  }
     * @return $this|\Closure
     */
    public function hookItemOperateRendering($callback = null)
    {
        if (null === $callback) {
            return $this->hookItemOperateRendering;
        }
        $this->hookItemOperateRendering = $callback;
        return $this;
    }
}
