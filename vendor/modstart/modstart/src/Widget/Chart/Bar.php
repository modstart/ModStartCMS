<?php


namespace ModStart\Widget\Chart;


use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\TimeUtil;

class Bar extends Chart
{
    protected $option = [
        'grid' => [
            // 'top' => '20%',
            'right' => '1%',
            'left' => '1%',
            'bottom' => '10%',
            'containLabel' => true
        ],
        'toolbox' => [
            'feature' => [
                'dataView' => [
                    'show' => true,
                    'readOnly' => false,
                ],
                'restore' => [
                    'show' => true,
                ],
                'saveAsImage' => [
                    'show' => true,
                ],
            ],
        ],
        'tooltip' => [
            'trigger' => 'axis',
            'axisPointer' => [
                'type' => 'shadow',
                'snap' => true,
                'crossStyle' => [
                    'color' => '#999',
                ],
            ],
        ],
        'legend' => [
            'data' => [],
        ],
        'xAxis' => [
            'type' => 'category',
            'data' => []
        ],
        'yAxis' => [
            'type' => 'value',
            'minInterval' => 1
        ],
        'series' => [

        ]
    ];

    public function ySeries($i, $value, $name = '数量', $param = [])
    {
        if (!isset($param['barColor'])) {
            $param['barColor'] = ColorUtil::pick('L-' . $i);
        }
        $this->option['legend']['data'][$i] = $name;
        $this->option['series'][$i] = [
            'name' => $name,
            'data' => $value,
            'type' => 'bar',
            'smooth' => true,
            'itemStyle' => [
                'normal' => [
                    'color' => $param['barColor'],
                    'barStyle' => [
                        'color' => $param['barColor'],
                    ]
                ]
            ]
        ];
        return $this;
    }

    public function tableDailyCountLatest($series = [], $limit = 15)
    {
        $end = date('Y-m-d');
        $endTs = strtotime($end);
        $startTs = $endTs - ($limit - 1) * TimeUtil::PERIOD_DAY;
        $start = date('Y-m-d', $startTs);
        return $this->tableDailyCount($start, $end, $series);
    }

    public function tableDailyCount($start, $end, $series = [])
    {
        $data = $this->dsTableCountSeriesDaily($start, $end, $series);
        $this->xData($data['time']);
        $this->option['series'] = [];
        foreach ($data['values'] as $index => $value) {
            $color = isset($series[$index]['color']) ? $series[$index]['color'] : ColorUtil::randomColor();
            $this->option['series'][] = [
                'name' => $series[$index]['title'],
                'data' => $value,
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => $color,
                        'barStyle' => [
                            'color' => $color
                        ]
                    ]
                ]
            ];
        }
        return $this;
    }

    public function tableDailySumLatest($series = [], $limit = 15)
    {
        $end = date('Y-m-d');
        $endTs = strtotime($end);
        $startTs = $endTs - ($limit - 1) * TimeUtil::PERIOD_DAY;
        $start = date('Y-m-d', $startTs);
        return $this->tableDailySum($start, $end, $series);
    }

    public function tableDailySum($start, $end, $series = [])
    {
        $data = $this->dsTableSumSeriesDaily($start, $end, $series);
        $this->xData($data['time']);
        $this->option['series'] = [];
        foreach ($data['values'] as $index => $value) {
            $color = isset($series[$index]['color']) ? $series[$index]['color'] : ColorUtil::randomColor();
            $this->option['series'][] = [
                'name' => $series[$index]['title'],
                'data' => $value,
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => $color,
                        'barStyle' => [
                            'color' => $color
                        ]
                    ]
                ]
            ];
        }
        return $this;
    }

}
