<?php


namespace ModStart\Repository\Filter;

use Closure;

trait HasRepositoryFilter
{
    
    private $repositoryFilter;

    private function setupRepositoryFilter()
    {
        $this->repositoryFilter = new RepositoryFilter();
    }

    
    public function repositoryFilter(Closure $callback = null)
    {
        if (null === $callback) {
            return $this->repositoryFilter;
        }
        call_user_func($callback, $this->repositoryFilter);
        return $this;
    }
}
