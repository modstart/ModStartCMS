<?php


namespace ModStart\Widget\Chart;


use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\ReportUtil;
use ModStart\Core\Util\TimeUtil;

class Scatter extends Chart
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

    public function random()
    {
        $this->xData(RandomUtil::dateCollection());
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
            'type' => 'scatter',
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

    /**
     * @param $series
     * @param $limit
     * @return $this
     * @deprecated
     */
    public function tableDailyCountLatest($series = [], $limit = 15)
    {
        $end = date('Y-m-d');
        $endTs = strtotime($end);
        $startTs = $endTs - ($limit - 1) * TimeUtil::PERIOD_DAY;
        $start = date('Y-m-d', $startTs);
        return $this->tableDailyCount($start, $end, $series);
    }

    /**
     * @param $start
     * @param $end
     * @param $series
     * @return $this
     * @deprecated
     */
    public function tableDailyCount($start, $end, $series = [])
    {
        $data = ReportUtil::tableCountSeriesDaily($start, $end, $series);
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

    /**
     * @param $series
     * @param $limit
     * @return $this
     * @deprecated
     */
    public function tableDailySumLatest($series = [], $limit = 15)
    {
        $end = date('Y-m-d');
        $endTs = strtotime($end);
        $startTs = $endTs - ($limit - 1) * TimeUtil::PERIOD_DAY;
        $start = date('Y-m-d', $startTs);
        return $this->tableDailySum($start, $end, $series);
    }

    /**
     * @param $start
     * @param $end
     * @param $series
     * @return $this
     * @deprecated
     */
    public function tableDailySum($start, $end, $series = [])
    {
        $data = ReportUtil::tableSumSeriesDaily($start, $end, $series);
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
