<?php


namespace ModStart\Field;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Field\Concern\CanCascadeFields;
use ModStart\Repository\TreeRepositoryInterface;

class Select extends AbstractField
{
    use CanCascadeFields;

    protected function setup()
    {
        $this->addVariables([
            'options' => [],
            'onValueChangeJsFunction' => '',
        ]);
    }

    public function options($options)
    {
        $this->addVariables(['options' => $options]);
        return $this;
    }

    public function optionsValue($options)
    {
        $this->addVariables(['options' => array_build($options, function ($k, $v) {
            return [$v, $v];
        })]);
        return $this;
    }

    public function optionArray($options, $idName = 'id', $titleName = 'title')
    {
        $options = array_build($options, function ($k, $v) use ($idName, $titleName) {
            return [$v[$idName], $v[$titleName]];
        });
        return $this->options($options);
    }

    public function onValueChangeJsFunction($jsFunction)
    {
        $this->addVariables(['onValueChangeJsFunction' => $jsFunction]);
        return $this;
    }

    public function optionType($cls)
    {
        return $this->options($cls::getList());
    }

    public function optionModel($table, $keyName = 'id', $labelName = 'name', $where = [])
    {
        return $this->options(ModelUtil::valueMap($table, $keyName, $labelName, $where));
    }

    public function optionModelTree($table, $idName = 'id', $pidName = 'pid', $titleName = 'title', $sortName = 'sort', $where = [])
    {
        $items = ModelUtil::model($table)->where($where)->get([$idName, $pidName, $titleName, $sortName]);
        $items = TreeUtil::itemsMergeLevel($items, $idName, $pidName, $sortName);
        return $this->optionItems($items, $idName, $titleName);
    }

    public function optionRepositoryTreeItems(TreeRepositoryInterface $repository, $treeMaxLevel = 0)
    {
        $items = $repository->getTreeItems($this->context);
        return $this->optionItems($items, $repository->getKeyName(), $repository->getTreeTitleColumn(), $treeMaxLevel);
    }

    public function optionItems($items, $idName = 'id', $titleName = 'title', $treeMaxLevel = 0)
    {
        $options = [];
        $options[0] = L('Root');
        foreach ($items as $i => $item) {
            if ($treeMaxLevel > 0) {
                if ($item->_level > $treeMaxLevel - 1) {
                    continue;
                }
            }
            $options[$item->{$idName}] = str_repeat('├', $item->_level) . $item->{$titleName};
        }
        return $this->options($options);
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }


}
