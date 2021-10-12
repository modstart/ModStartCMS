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
        "grid" => [
            "top" => "5%",
            "right" => "1%",
            "left" => "1%",
            "bottom" => "10%",
            "containLabel" => true
        ],
        "tooltip" => [
            "trigger" => "axis"
        ],
        "xAxis" => [
            "type" => "category",
            "data" => []
        ],
        "yAxis" => [
            "type" => "value",
            "minInterval" => 1
        ],
        "series" => [
            [
                "name" => "数量",
                "data" => [],
                "type" => "line",
                "smooth" => true,
                "itemStyle" => [
                    "normal" => [
                        "color" => "#4F7FF3",
                        "lineStyle" => [
                            "color" => "#4F7FF3"
                        ]
                    ]
                ]
            ]
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

    public function random()
    {
        $this->option['xAxis']['data'] = RandomUtil::dateCollection();
        $this->option['series'][0]['data'] = RandomUtil::numberCollection();
        return $this;
    }

    public function xData($value)
    {
        $this->option['xAxis']['data'] = $value;
        return $this;
    }

    public function yData($value)
    {
        $this->option['series'][0]['data'] = $value;
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
                "name" => $series[$index]['title'],
                "data" => $value,
                "type" => "line",
                "smooth" => true,
                "itemStyle" => [
                    "normal" => [
                        "color" => $color,
                        "lineStyle" => [
                            "color" => $color
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
                "name" => $series[$index]['title'],
                "data" => $value,
                "type" => "line",
                "smooth" => true,
                "itemStyle" => [
                    "normal" => [
                        "color" => $color,
                        "lineStyle" => [
                            "color" => $color
                        ]
                    ]
                ]
            ];
        }
        return $this;
    }

}
