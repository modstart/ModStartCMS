<?php


namespace ModStart\Field;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
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
            'onRemoteLoadJsFunction' => '',
            'selectSearch' => false,
            'optionRemote' => null,
            'optionRemoteAutoInit' => true,
        ]);
    }

    public function selectSearch($value)
    {
        $this->addVariables(['selectSearch' => $value]);
        return $this;
    }

    public function optionRemote($value, $autoInit = true)
    {
        $this->addVariables([
            'optionRemote' => $value,
            'optionRemoteAutoInit' => $autoInit,
        ]);
        return $this;
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

    public function onRemoteLoadJsFunction($jsFunction)
    {
        $this->addVariables(['onRemoteLoadJsFunction' => $jsFunction]);
        return $this;
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
        $items = ModelUtil::autoModel($table)->where($where)->get([$idName, $pidName, $titleName, $sortName]);
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
        if ($treeMaxLevel > 0) {
            $items = $items->filter(function ($item) use ($treeMaxLevel) {
                return $item->_level <= $treeMaxLevel - 1;
            });
        }
        $prevLevel = 0;
        $titles = [];
        foreach ($items as $i => $item) {
            $prefix = TreeUtil::itemLevelPrefix($item->_level);
            if ($item->_level > $prevLevel) {
                array_push($titles, $item->{$titleName});
            } else {
                while ($item->_level < $prevLevel) {
                    array_pop($titles);
                    $prevLevel--;
                }
                array_pop($titles);
                array_push($titles, $item->{$titleName});
            }
            $prevLevel = $item->_level;
            $options[$item->{$idName}] = [
                'label' => $prefix . $item->{$titleName},
                'title' => join('-', $titles)
            ];
        }
        return $this->options($options);
    }

    public static function optionRemoteHandleModel($table, $valueKey = 'id', $labelKey = 'title', $param = [])
    {
        if (!isset($param['sort'])) {
            $param['sort'] = ['id', 'desc'];
        }
        $input = InputPackage::buildFromInput();
        $value = $input->getInteger('value');
        $keywords = $input->getTrimString('keywords');
        $query = ModelUtil::model($table);
        if ($value) {
            $query = $query->where($valueKey, $value);
        }
        if ($keywords) {
            $query = $query->where($labelKey, 'like', '%' . $keywords . '%');
        }
        $records = $query
            ->orderBy($param['sort'][0], $param['sort'][1])
            ->limit(10)
            ->get([$valueKey, $labelKey])->toArray();
        $options = [];
        foreach ($records as $record) {
            $options[] = [
                'value' => $record[$valueKey],
                'label' => $record[$labelKey],
            ];
        }
        return Response::generateSuccessData([
            'options' => $options,
        ]);
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }

}
