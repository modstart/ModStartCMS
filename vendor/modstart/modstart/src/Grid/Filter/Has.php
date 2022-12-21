<?php


namespace ModStart\Grid\Filter;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Type\BaseType;

class Has extends AbstractFilter
{
    protected $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['has']) && is_array($searchInfo['has'])) {
            return $this->buildCondition($this->column, $searchInfo['has']);
        }
        return null;
    }

    /**
     * 使用 Checkbox 作为搜索条件
     * @param mixed $options array | BaseType
     * @return $this
     */
    public function checkbox($options)
    {
        $this->field = new Field\Checkbox($this);
        $this->field->options($options);
        return $this;
    }

    /**
     * 使用 树状选择器 作为搜索条件
     * @param $options array | BaseType
     * @return $this
     */
    public function cascader($options)
    {
        $this->field = new Field\Cascader($this);
        $this->field->nodes($options);
        return $this;
    }

    /**
     * 使用 树状选择器 作为搜索条件
     * @param $table string 数据表
     * @param $idKey string 主键字段名
     * @param $pidKey string 父级主键字段名
     * @param $titleKey string 标题字段名
     * @param $sortKey string 排序字段名
     * @return $this
     */
    public function cascaderModel($table, $idKey = 'id', $pidKey = 'pid', $titleKey = 'title', $sortKey = 'sort')
    {
        $nodes = [];
        foreach (ModelUtil::all($table, [], [$idKey, $pidKey, $titleKey, $sortKey], [$sortKey, 'asc']) as $item) {
            $nodes[] = [
                'id' => $item[$idKey],
                'title' => $item[$titleKey],
                'pid' => $item[$pidKey],
                'sort' => $item[$sortKey],
            ];
        }
        return $this->cascader($nodes);
    }
}
