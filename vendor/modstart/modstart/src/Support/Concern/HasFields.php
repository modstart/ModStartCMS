<?php


namespace ModStart\Support\Concern;

use Illuminate\Support\Collection;
use ModStart\Field\AbstractField;
use ModStart\Field\CascadeGroup;
use ModStart\Field\Html;
use ModStart\Field\Type\FieldRenderMode;


trait HasFields
{
    
    private $fields;
    
    private $fieldDefaultRenderMode = 'add';

    private function setupFields()
    {
        $this->fields = new Collection();
    }

    
    public function fillFields()
    {
        $this->fields()->each(function (AbstractField $field) {
            $field->fill($this->item);
        });
    }

    
    public function pushField(AbstractField $field)
    {
        $this->fields()->push($field);
        return $this;
    }

    
    public function removeField($column)
    {
        $this->fields = $this->fields()->filter(function (AbstractField $field) use ($column) {
            return $field->column() != $column;
        });
        return $this;
    }

    
    public function prependField(AbstractField $field)
    {
        $this->fields()->prepend($field);
        return $this;
    }

    public function fieldDefaultRenderMode($value = null)
    {
        if (null === $value) {
            return $this->fieldDefaultRenderMode;
        }
        return $this->fieldDefaultRenderMode = $value;
    }

    
    public function fields()
    {
        return $this->fields;
    }

    
    public function listableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->listable();
        });
    }

    
    public function addableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->addable();
        });
    }

    
    protected function editableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->editable();
        });
    }

    
    protected function showableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->showable();
        });
    }

    
    public function sortableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->sortable();
        });
    }

}
