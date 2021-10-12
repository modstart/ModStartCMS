<?php


namespace ModStart\Repository\Filter;

use Closure;

trait HasRepositoryFilter
{
    /** @var RepositoryFilter */
    private $repositoryFilter;

    private function setupRepositoryFilter()
    {
        $this->repositoryFilter = new RepositoryFilter();
    }

    /**
     * Set the grid filter.
     *
     * @param Closure $callback function(RepositoryFilter $filter){ $filter->where('userId','1'); }
     * @return $this|RepositoryFilter
     */
    public function repositoryFilter(Closure $callback = null)
    {
        if (null === $callback) {
            return $this->repositoryFilter;
        }
        call_user_func($callback, $this->repositoryFilter);
        return $this;
    }
}
