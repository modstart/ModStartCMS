<?php

namespace ModStart\Repository;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Model;
use ModStart\Support\Concern\HasArguments;

abstract class Repository implements RepositoryInterface, SortRepositoryInterface, TreeRepositoryInterface
{
    use Macroable;
    use HasArguments;

    
    protected $keyName = 'id';

    
    protected $isSoftDeletes = false;

    
    public function getKeyName()
    {
        return $this->keyName ? $this->keyName : 'id';
    }

    
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    
    public function getCreatedAtColumn()
    {
        return 'created_at';
    }

    
    public function getUpdatedAtColumn()
    {
        return 'updated_at';
    }

    
    public function isSoftDeletes()
    {
        return $this->isSoftDeletes;
    }

    
    public function setIsSoftDeletes($isSoftDeletes)
    {
        $this->isSoftDeletes = $isSoftDeletes;
    }

    public function add(Form $form)
    {
        throw new RuntimeException('This repository does not support "add" method.');
    }

    public function editing(Form $form)
    {
        throw new RuntimeException('This repository does not support "editing" method.');
    }

    public function edit(Form $form)
    {
        throw new RuntimeException('This repository does not support "edit" method.');
    }

    public function show(Detail $detail)
    {
        throw new RuntimeException('This repository does not support "show" method.');
    }

    public function deleting(Form $form)
    {
        throw new RuntimeException('This repository does not support "delete" method.');
    }

    public function delete(Form $form, Arrayable $deletingData)
    {
        throw new RuntimeException('This repository does not support "delete" method.');
    }

    public function get(Model $model)
    {
        throw new RuntimeException('This repository does not support "get" method.');
    }

    private $sortColumn = 'sort';

    
    public function getSortColumn()
    {
        return $this->sortColumn;
    }

    
    public function setSortColumn($value)
    {
        $this->sortColumn = $value;
    }

    public function sortEdit(Form $form)
    {
        throw new RuntimeException('This repository does not support "sortEdit" method.');
    }

    private $treePidColumn = 'pid';
    private $treeTitleColumn = 'title';
    private $treeSortColumn = 'sort';

    public function getTreePidColumn()
    {
        return $this->treePidColumn;
    }

    public function setTreePidColumn($value)
    {
        $this->treePidColumn = $value;
    }

    public function getTreeTitleColumn()
    {
        return $this->treeTitleColumn;
    }


    public function setTreeTitleColumn($value)
    {
        $this->treeTitleColumn = $value;
    }

    public function getTreeSortColumn()
    {
        return $this->treeSortColumn;
    }

    public function setTreeSortColumn($value)
    {
        $this->treeSortColumn = $value;
    }

    public function getTreeItems()
    {
        throw new RuntimeException('This repository does not support "getTreeItems" method.');
    }

    public function getTreeAncestorItems()
    {
        throw new RuntimeException('This repository does not support "getTreeAncestorItems" method.');
    }

    
    public static function make(...$params)
    {
        return new static(...$params);
    }

    
    public static function instance($repository, array $args = [])
    {
        
        if (null === $repository) {
            return null;
        }
        if (is_string($repository)) {
            $repository = new $repository($args);
        }
        if ($repository instanceof \Illuminate\Database\Eloquent\Model
            || $repository instanceof \Illuminate\Database\Eloquent\Builder) {
            $repository = EloquentRepository::make($repository);
        }
        if (!$repository instanceof \ModStart\Repository\Repository) {
            $class = is_object($repository) ? get_class($repository) : $repository;
            throw new \InvalidArgumentException("The class [{$class}] must be a type of [" . Repository::class . '].');
        }
        return $repository;
    }
}
