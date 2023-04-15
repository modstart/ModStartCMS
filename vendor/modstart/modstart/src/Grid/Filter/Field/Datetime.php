<?php

namespace ModStart\Grid\Filter\Field;

class Datetime extends AbstractFilterField
{
    protected $quickSelect = [];

    protected function setup()
    {
        $this->addFluentAttributeVariable('quickSelect');
    }

    public function quickSelect($quickSelect = [])
    {
        if (null === $quickSelect) {
            return $this->quickSelect;
        }
        if (count($quickSelect) == 0) {
            $quickSelect = [
                [
                    'label' => L('Today'),
                    'min' => date('Y-m-d 00:00:00'),
                    'max' => date('Y-m-d 23:59:59'),
                ],
                [
                    'label' => L('Yesterday'),
                    'min' => date('Y-m-d 00:00:00', strtotime('-1 day')),
                    'max' => date('Y-m-d 23:59:59', strtotime('-1 day')),
                ]
            ];
        }
        $this->quickSelect = $quickSelect;
        return $this;
    }
}
