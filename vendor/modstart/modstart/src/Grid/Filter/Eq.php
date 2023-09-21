<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;

class Eq extends AbstractFilter
{
    public function cascader($options)
    {
        $this->field = new Field\Cascader($this);
        $this->field->options($options);
        return $this;
    }

    /**
     * @param mixed $options array | BaseType
     * @return $this
     */
    public function select($options)
    {
        $this->field = new Field\Select($this);
        $this->field->options($options);
        return $this;
    }

    public function selectArray($options, $idName = 'id', $titleName = 'title')
    {
        $options = array_build($options, function ($k, $v) use ($idName, $titleName) {
            return [$v[$idName], $v[$titleName]];
        });
        return $this->select($options);
    }

    public function selectModel($table, $keyName = 'id', $labelName = 'name', $where = [])
    {
        return $this->select(ModelUtil::valueMap($table, $keyName, $labelName, $where));
    }

    public function selectLevelItems($items, $idName = 'id', $titleName = 'title', $treeMaxLevel = 0)
    {
        $options = [];
        $options[0] = L('Root');
        foreach ($items as $i => $item) {
            if ($treeMaxLevel > 0) {
                if ($item->_level > $treeMaxLevel - 1) {
                    continue;
                }
            }
            $options[$item->{$idName}] = TreeUtil::itemLevelPrefix($item->_level) . $item->{$titleName};
        }
        return $this->select($options);
    }

    public function selectModelTree($table, $idName = 'id', $pidName = 'pid', $titleName = 'title', $sortName = 'sort', $where = [])
    {
        $items = ModelUtil::autoModel($table)->where($where)->get([$idName, $pidName, $titleName, $sortName]);
        $items = TreeUtil::itemsMergeLevel($items, $idName, $pidName, $sortName);
        return $this->selectLevelItems($items, $idName, $titleName);
    }

    /**
     * @param mixed $options array | BaseType
     * @return $this
     */
    public function radio($options)
    {
        $this->field = new Field\Radio($this);
        $this->field->options($options);
        return $this;
    }

    public function switchRadioYesNo()
    {
        $this->field = new Field\Radio($this);
        $this->field->options([
            '0' => L('No'),
            '1' => L('Yes')
        ]);
        return $this;
    }

    public function switchRadioOnOff()
    {
        $this->field = new Field\Radio($this);
        $this->field->options([
            '0' => L('Off'),
            '1' => L('On')
        ]);
        return $this;
    }

    public function switchSelectYesNo()
    {
        $this->field = new Field\Select($this);
        $this->field->options([
            '0' => L('No'),
            '1' => L('Yes')
        ]);
        return $this;
    }

    public function switchSelectOnOff()
    {
        $this->field = new Field\Select($this);
        $this->field->options([
            '0' => L('Off'),
            '1' => L('On')
        ]);
        return $this;
    }
}
