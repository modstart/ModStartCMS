<?php


namespace ModStart\Repository\Filter;


use Illuminate\Support\Facades\Input;

trait HasScopeFilter
{
    /** @var array */
    protected $scopeFilters = [];
    /** @var string null */
    protected $scopeDefault = null;
    /** @var array 自动保存的内容 */
    protected $scopeAddedParam = [];

    /**
     * Set the scope filter.
     *
     * @param string $name
     * @param string $title
     * @param Closure $callback function(ScopeFilter $filter){ $filter->where('userId','1'); }
     * @return $this
     */
    public function scopeFilter($name, $title, \Closure $callback = null)
    {
        $filter = new ScopeFilter();
        call_user_func($callback, $filter);
        array_push($this->scopeFilters, [
            'name' => $name,
            'title' => $title,
            'filter' => $filter,
        ]);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function scopeDefault($name)
    {
        $this->scopeDefault = $name;
        return $this;
    }

    /**
     * 获取 scope 参数，包含默认参数，通常用于带参数的列表
     * @return array
     */
    public function scopeParam()
    {
        $scopeValue = $this->scopeValue();
        if (null === $scopeValue) {
            return [];
        }
        return [
            '_scope' => $scopeValue,
        ];
    }

    public function scopeValue()
    {
        return Input::get('_scope', $this->scopeDefault);
    }

    /**
     * 保存自动追加参数
     *
     * @param null $param
     * @return $this|array
     */
    public function scopeAddedParam($param = null)
    {
        if (is_null($param)) {
            return $this->scopeAddedParam;
        }
        $this->scopeAddedParam = $param;
        return $this;
    }

    public function scopeExecuteQueries(&$query)
    {
        $scope = Input::get('_scope', $this->scopeDefault);
        if (empty($scope)) {
            return;
        }
        foreach ($this->scopeFilters as $scopeFilter) {
            if ($scopeFilter['name'] == $scope) {
                /** @var ScopeFilter $filter */
                $filter = $scopeFilter['filter'];
                $filter->executeQueries($query);
            }
        }
    }
}
