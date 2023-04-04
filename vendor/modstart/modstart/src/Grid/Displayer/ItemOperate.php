<?php


namespace ModStart\Grid\Displayer;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use ModStart\Field\AbstractField;
use ModStart\Widget\TextAction;
use ModStart\Widget\TextLink;

/**
 * Class ItemOperate
 * @package ModStart\Grid\Displayer
 *
 *
 * @method ItemOperate|Model|\stdClass item($value = null)
 * @method ItemOperate|integer index($value = null)
 * @method ItemOperate|mixed canShow($value = null)
 * @method ItemOperate|mixed canEdit($value = null)
 * @method ItemOperate|mixed canDelete($value = null)
 * @method ItemOperate|mixed canCopy($value = null)
 */
class ItemOperate extends AbstractDisplayer
{
    /** @var AbstractField */
    protected $field;
    protected $item;
    protected $index;
    protected $canShow;
    protected $canEdit;
    protected $canDelete;
    protected $canSort;
    protected $canCopy;
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
        'canCopy',
    ];

    public function reset()
    {
        $this->item = null;
        $this->index = null;
        $this->canShow = $this->grid->canShow();
        $this->canEdit = $this->grid->canEdit();
        $this->canDelete = $this->grid->canDelete();
        $this->canSort = $this->grid->canSort();
        $this->canCopy = $this->grid->canCopy() && $this->grid->canAdd();
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
        }
        if ($this->canShow() && $this->grid->urlShow()) {
            $this->operates[] = TextAction::primary(L('Show'), 'data-show');
        }
        if ($this->canEdit() && $this->grid->urlEdit()) {
            $editText = $this->grid->textEdit();
            if (empty($editText)) {
                $editText = L('Edit');
            }
            if ($this->grid->editBlankPage()) {
                $this->operates[] = TextLink::primary($editText,
                    $this->grid->urlEdit() . '?_id=' . $this->item->{$this->grid->getRepositoryKeyName()},
                    modstart_admin_is_tab() ? 'data-tab-open' : ''
                );
            } else {
                $this->operates[] = TextAction::primary($editText, 'data-edit');
            }
        }
        if ($this->canDelete() && $this->grid->urlDelete()) {
            $this->operates[] = TextAction::danger(L('Delete'), 'data-delete');
        }
        if ($this->canCopy()) {
            $this->operates[] = TextAction::muted(L('Copy'), 'data-add-button data-add-copy-button');
        }
        $this->operates = array_merge($this->operates, $this->appendOperates);
        return join('', $this->operates);
    }

}
