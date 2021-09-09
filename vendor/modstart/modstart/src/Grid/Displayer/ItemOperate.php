<?php


namespace ModStart\Grid\Displayer;


use Illuminate\Database\Eloquent\Model;
use ModStart\Field\AbstractField;
use ModStart\Widget\TextAction;
use ModStart\Widget\TextLink;


class ItemOperate extends AbstractDisplayer
{
    
    protected $field;
    protected $item;
    protected $index;
    protected $canShow;
    protected $canEdit;
    protected $canDelete;
    protected $canSort;
    protected $operates = [];
    protected $onlyOperate = null;
    protected $prependOperates = [];
    protected $appendOperates = [];
    protected $fluentAttributes = [
        'item',
        'index',
        'canShow',
        'canEdit',
        'canDelete',
        'canSort',
    ];

    public function reset()
    {
        $this->item = null;
        $this->index = null;
        $this->canShow = $this->grid->canShow();
        $this->canEdit = $this->grid->canEdit();
        $this->canDelete = $this->grid->canDelete();
        $this->canSort = $this->grid->canSort();
        $this->resetOperates();
        return $this;
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }

    public function resetOperates()
    {
        $this->onlyOperate = null;
        $this->prependOperates = [];
        $this->appendOperates = [];
    }

    public function only($operate)
    {
        $this->onlyOperate = $operate;
        return $this;
    }

    public function push($operate)
    {
        $this->appendOperates[] = $operate;
        return $this;
    }

    public function prepend($operate)
    {
        if (is_array($operate)) {
            $this->prependOperates = array_merge($operate, $this->prependOperates);
        } else {
            $this->prependOperates[] = $operate;
        }
    }

    public function render()
    {
        $autoWidth = empty($this->field->width());
        if ($autoWidth) {
            $this->field->width(200);
        }
        if (null !== $this->onlyOperate) {
            return $this->onlyOperate;
        }
        $this->operates = [];
        $this->operates = array_merge($this->operates, $this->prependOperates);
        if ($this->canSort() && $this->grid->urlSort()) {
            $this->operates[] = TextAction::primary('<i class="iconfont icon-top"></i>', 'data-sort="top"');
            $this->operates[] = TextAction::primary('<i class="iconfont icon-direction-up"></i>', 'data-sort="up"');
            $this->operates[] = TextAction::primary('<i class="iconfont icon-direction-down"></i>', 'data-sort="down"');
            $this->operates[] = TextAction::primary('<i class="iconfont icon-bottom"></i>', 'data-sort="bottom"');
            if ($autoWidth) {
                $this->field->width(400);
            }
        }
        if ($this->canShow() && $this->grid->urlShow()) {
            $this->operates[] = TextAction::primary(L('Show'), 'data-show');
        }
        if ($this->canEdit() && $this->grid->urlEdit()) {
            if ($this->grid->editBlankPage()) {
                $this->operates[] = TextLink::primary(L('Edit'), $this->grid->urlEdit() . '?_id=' . $this->item->{$this->grid->getRepositoryKeyName()});
            } else {
                $this->operates[] = TextAction::primary(L('Edit'), 'data-edit');
            }
        }
        if ($this->canDelete() && $this->grid->urlDelete()) {
            $this->operates[] = TextAction::danger(L('Delete'), 'data-delete');
        }
        $this->operates = array_merge($this->operates, $this->appendOperates);
        return join('', $this->operates);
    }

}