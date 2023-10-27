<?php


namespace ModStart\Field;


use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Field\Type\FieldRenderMode;

class Tree extends AbstractField
{
    private $columnNames = [
        'id' => 'id',
        'title' => 'title',
        'children' => '_child',
    ];

    protected function setup()
    {
        $this->addVariables([
            'spread' => true,
            'nodes' => [],
        ]);
    }

    /**
     * 设置id字段映射
     * @param $value
     * @return $this
     */
    public function columnNameId($value)
    {
        $this->columnNames['id'] = $value;
        return $this;
    }

    /**
     * 设置title字段映射
     * @param $value
     * @return $this
     */
    public function columnNameTitle($value)
    {
        $this->columnNames['title'] = $value;
        return $this;
    }

    /**
     * 设置children字段映射
     * @param $value
     * @return $this
     */
    public function columnNameChildren($value)
    {
        $this->columnNames['children'] = $value;
        return $this;
    }

    public function spread($value)
    {
        $this->addVariables(['spread' => $value]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        return ConvertUtil::toArray($value);
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        return ConvertUtil::toArray($value);
    }

    /**
     * 设置树状节点的待选值
     *
     * @param \Closure|array $tree
     * @return $this
     */
    public function tree($tree)
    {
        if ($tree instanceof \Closure) {
            $tree = $tree($this);
        }
        $this->addVariables(['nodes' => $this->formatNodes($tree)]);
        return $this;
    }

    private function formatNodes($tree)
    {
        $nodes = [];
        foreach ($tree as $item) {
            $newItem = [];
            $newItem['spread'] = $this->getVariable('spread', true);
            $newItem['id'] = $item[$this->columnNames['id']];
            $newItem['title'] = $item[$this->columnNames['title']];
            if (!empty($item[$this->columnNames['children']])) {
                $newItem['children'] = $this->formatNodes($item[$this->columnNames['children']]);
            }
            $nodes[] = $newItem;
        }
        return $nodes;
    }

}
