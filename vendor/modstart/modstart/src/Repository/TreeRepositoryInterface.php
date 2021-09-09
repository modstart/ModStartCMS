<?php

namespace ModStart\Repository;

use Illuminate\Support\Collection;
use ModStart\Form\Form;
use ModStart\Grid\Model;

interface TreeRepositoryInterface
{
    
    public function getKeyName();

    
    public function getTreePidColumn();

    
    public function getTreeTitleColumn();

    
    public function getTreeSortColumn();

    
    public function getTreeItems();
    
}
