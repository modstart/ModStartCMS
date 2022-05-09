<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Dao\ModelUtil;

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
