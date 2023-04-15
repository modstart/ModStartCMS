<?php


namespace ModStart\Widget\Chart;


use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Widget\AbstractWidget;
use ModStart\Widget\Traits\DSTrait;

class Line extends AbstractWidget
{
    use DSTrait;

    /**
     * @var string
     */
    protected $view = 'modstart::widget.chart';

    protected $height = 300;
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
                'type' => 'cross',
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

    /**
     * Line constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function make()
    {
        return new static();
    }

    public function option($value = null)
    {
        if (is_null($value)) {
            return $this->option;
        }
        $this->option = $value;
        return $this;
    }

    public function optionSet($key, $value)
    {
        $this->option[$key] = $value;
        return $this;
    }

    public function random()
    {
        $this->option['xAxis']['data'] = RandomUtil::dateCollection();
        $this->ySeries(0, RandomUtil::numberCollection());
        return $this;
    }

    public function xData($value, $param = [])
    {
        $this->option['xAxis']['data'] = $value;
        return $this;
    }

    public function yData($value, $name = '数量', $param = [])
    {
        return $this->ySeries(0, $value, $name, $param);
    }

    public function ySeries($i, $value, $name = '数量', $param = [])
    {
        if (!isset($param['lineColor'])) {
            $param['lineColor'] = ColorUtil::pick('L-' . $i);
        }
        $this->option['legend']['data'][$i] = $name;
        $this->option['series'][$i] = [
            'name' => $name,
            'data' => $value,
            'type' => 'line',
            'smooth' => true,
            'itemStyle' => [
                'normal' => [
                    'color' => $param['lineColor'],
                    'lineStyle' => [
                        'color' => $param['lineColor'],
                    ]
                ]
            ]
        ];
        return $this;
    }

    protected function variables()
    {
        return [
            'option' => $this->option,
            'height' => $this->height,
        ];
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
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => $color,
                        'lineStyle' => [
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
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => $color,
                        'lineStyle' => [
                            'color' => $color
                        ]
                    ]
                ]
            ];
        }
        return $this;
    }

}
