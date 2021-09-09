<?php

namespace ModStart\Repository;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ModStart\Form\Form;
use ModStart\Detail\Detail;
use ModStart\Grid\Model;

interface RepositoryInterface
{
    
    public function getKeyName();

    
    public function getCreatedAtColumn();

    
    public function getUpdatedAtColumn();

    
    public function isSoftDeletes();

    
    public function get(Model $model);

    
    public function add(Form $form);

    
    public function editing(Form $form);

    
    public function edit(Form $form);

    
    public function show(Detail $detail);

    
    public function deleting(Form $form);

    
    public function delete(Form $form, Arrayable $deletingData);
}
