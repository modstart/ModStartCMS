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

    /**
     * 操作列初始化时回调
     * @var \Closure
     */
    private $hookItemOperateFieldBuild;

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
            if ($this->hookItemOperateFieldBuild) {
                call_user_func($this->hookItemOperateFieldBuild, $field);
            }
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
     * @param $callback \Closure function(ItemOperate $itemOperate){  }
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

    /**
     * @param $callback \Closure function(AbstractField $field){  }
     * @return $this|\Closure
     */
    public function hookItemOperateFieldBuild($callback = null)
    {
        if (null === $callback) {
            return $this->hookItemOperateFieldBuild;
        }
        $this->hookItemOperateFieldBuild = $callback;
        return $this;
    }

    /**
     * 操作栏浮动
     * @param $fixed string left|right
     * @return $this
     */
    public function operateFixed($fixed)
    {
        $this->hookItemOperateFieldBuild(function (AbstractField $field) use ($fixed) {
            $field->gridFixed($fixed);
        });
        return $this;
    }
}
