<?php


namespace ModStart\Widget\Chart;


class Pie extends Chart
{
    protected $option = [
        'grid' => [
            'top' => '20%',
            'right' => '0',
            'left' => '0',
            'bottom' => '0',
            'containLabel' => true
        ],
        'tooltip' => [
            'trigger' => 'item',
        ],
        'legend' => [
            'orient' => 'vertical',
            'left' => 'left',
        ],
        'series' => [
            [
                'type' => 'pie',
                'radius' => '80%',
                'data' => [],
                'emphasis' => [
                    'itemStyle' => [
                        'shadowBlur' => 10,
                        'shadowOffsetX' => 0,
                        'shadowColor' => 'rgba(0, 0, 0, 0.5)'
                    ]
                ]
            ]
        ]
    ];

    public function random()
    {
        $data = [];
        foreach ([
                     'Android', 'iOS', 'Windows', 'MacOS', 'Linux', 'Other',
                 ] as $k) {
            $data[] = [
                'name' => $k,
                'value' => rand(100, 1000),
            ];
        }
        $this->data($data);
        return $this;
    }

    public function data($data)
    {
        $this->option['series'][0]['data'] = $data;
        return $this;
    }

}
