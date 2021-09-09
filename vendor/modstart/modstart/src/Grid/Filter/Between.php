<?php

namespace ModStart\Grid\Filter;

class Between extends AbstractFilter
{
    
    protected $view = null;

    
    public function formatId($column)
    {
        $id = str_replace('.', '_', $column);

        return ['start' => "{$id}_start", 'end' => "{$id}_end"];
    }

    
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $columns[0];
        } else {
            $name = array_shift($columns);

            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        return ['start' => "{$name}[start]", 'end' => "{$name}[end]"];
    }

    
    public function condition()
    {
        if (!array_has($search, $this->column)) {
            return;
        }

        $this->value = array_get($search, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
        }

        if (!isset($value['start'])) {
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end'])) {
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        return $this->buildCondition($this->column, $this->value);
    }

    public function datetime($options = [])
    {
        $this->view = 'admin::filter.betweenDatetime';

        $this->prepareForDatetime($options);
    }

    protected function prepareForDatetime($options = [])
    {
        $options['format'] = array_get($options, 'format', 'YYYY-MM-DD HH:mm:ss');
        $options['locale'] = array_get($options, 'locale', config('app.locale'));

        $startOptions = json_encode($options);
        $endOptions = json_encode($options + ['useCurrent' => false]);
    }

    public function render()
    {
        if (isset($this->view)) {
            return view($this->view, $this->variables());
        }

        return parent::render();
    }
}
