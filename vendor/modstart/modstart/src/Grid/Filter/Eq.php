<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Type\BaseType;
use ModStart\Grid\Filter\Field\Radio;

class Eq extends AbstractFilter
{
    
    public function select($options)
    {
        $this->field = new Field\Select($this);
        $this->field->options($options);
        return $this;
    }

    public function selectModel($table, $keyName = 'id', $labelName = 'name', $where = [])
    {
        return $this->select(ModelUtil::valueMap($table, $keyName, $labelName, $where));
    }

    
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
