<?php


namespace ModStart\Repository\Filter;


use Illuminate\Support\Facades\Input;

trait HasScopeFilter
{
    /** @var array */
    protected $scopeFilters = [];

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

    public function scopeExecuteQueries(&$query)
    {
        $scope = Input::get('_scope');
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