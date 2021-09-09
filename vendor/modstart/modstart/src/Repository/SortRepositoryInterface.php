<?php

namespace ModStart\Repository;

use ModStart\Form\Form;

interface SortRepositoryInterface
{
    
    public function getKeyName();

    
    public function getSortColumn();

    
    public function sortEdit(Form $form);
}
