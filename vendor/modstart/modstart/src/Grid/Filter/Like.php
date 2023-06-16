<?php

namespace ModStart\Grid\Filter;

class Like extends AbstractFilter
{
    private $handle;

    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['like']) && $searchInfo['like'] !== '') {
            if (!empty($this->handle)) {
                return call_user_func_array($this->handle, [
                    $searchInfo['like']
                ]);
            } else {
                return $this->buildCondition($this->column, 'like', "%${searchInfo['like']}%");
            }
        }
        return null;
    }

    public function handle(\Closure $closure)
    {
        $this->handle = $closure;
    }
}
