<?php

namespace ModStart\Core\Util;


class LotteryUtil
{
    /**
     * 抽取一个奖品
     *
     * @param array $pool = array(
     *  ['id'=>xx,'rate'=>5.01],
     *  ['id'=>xx,'rate'=>5.00],
     *  ['id'=>xx,'rate'=>5.00],
     * )
     * @return id or null
     * @throws \Exception
     */
    public static function fetchPoll(array $pool)
    {
        $map = [];
        $index = 0;
        foreach ($pool as $item) {
            $space = intval(bcmul($item['rate'], 100, 2));
            if ($space <= 0) {
                continue;
            }
            for ($i = 0; $i < $space; $i++) {
                $map[$index++] = $item['id'];
            }
            if ($index > 10000) {
                throw new \Exception('bad lottery pool 10000');
            }
        }
        while ($index < 10000) {
            $map[$index++] = null;
        }
        $index = rand(0, 9999);
        return $map[$index];
    }

    /**
     * 返回一个取值范围（包含 min 和 max）
     *
     * @param $min : 0.00
     * @param $max : 1.00
     * @return string
     */
    public static function randomMoneyInRange($min, $max)
    {
        $redbagValue = rand(
            intval(bcmul($min, 100, 2)),
            intval(bcmul($max, 100, 2))
        );
        return bcdiv($redbagValue, 100, 2);
    }


    private static function generate($code, $msg, $data = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        if (null === $data) {
            unset($response['data']);
        }
        return $response;
    }

    public static function money($moneyTotal, $number, $moneyPerMin, $moneyPerMax)
    {
        $min = intval(bcmul($moneyPerMin, 100, 2));
        $max = intval(bcmul($moneyPerMax, 100, 2));
        $total = intval(bcmul($moneyTotal, 100, 2));
        $number = intval($number);

        if ($total < $min * $number) {
            return self::generate(-1, '金额最少为' . sprintf('%0.2f', bcdiv($min * $number, 100, 2)));
        }
        if ($total > $max * $number) {
            return self::generate(-1, '金额最多为' . sprintf('%0.2f', bcdiv($max * $number, 100, 2)));
        }
        if ($number < 1 || $number > 100000) {
            return self::generate(-1, '数量范围为 1-100000');
        }
        if ($total < 1 || $total > 1000000 * 100) {
            return self::generate(-1, '金额范围为 0.01-1000000.00');
        }

        $leftTotal = $total - $min * $number;

        $moneys = [];
        for ($i = 0, $diff = $max - $min; $i < $number; $i++) {
            if ($leftTotal > 0) {
                $moneyRandom = rand(0, $diff);
                if ($moneyRandom > $leftTotal) {
                    $moneyRandom = $leftTotal;
                }
                $leftTotal -= $moneyRandom;
            } else {
                $moneyRandom = 0;
            }
            $moneys[] = $min + $moneyRandom;
        }

        if ($leftTotal > 0) {
            foreach ($moneys as &$money) {
                $diff = $max - $money;
                if (!$diff) {
                    continue;
                }
                if ($diff <= $leftTotal) {
                    $money += $diff;
                    $leftTotal -= $diff;
                } else {
                    $money += $leftTotal;
                    $leftTotal = 0;
                    break;
                }
            }
        }

        $total = bcdiv(array_sum($moneys), 100, 2);
        foreach ($moneys as &$money) {
            $money = bcdiv($money, 100, 2);
        }

        shuffle($moneys);

        return self::generate(0, null, [
            'total' => $total,
            'min' => bcdiv($min, 100, 2),
            'max' => bcdiv($max, 100, 2),
            'number' => $number,
            'moneys' => $moneys,
        ]);
    }

    /**
     * 根据总金额、总数量、最小值、最大值抽取下一个随机的数字
     *
     * @param $moneyTotal
     * @param $number
     * @param null $moneyPerMin
     * @param null $moneyPerMax
     * @return array
     */
    public static function next($moneyTotal, $number, $moneyPerMin = null, $moneyPerMax = null)
    {
        if (null === $moneyPerMin) {
            $total = bcmul($moneyTotal, 100, 2);
            $moneyPerMin = max(intval($total / $number / 2), 1);
            $moneyPerMin = bcdiv($moneyPerMin, 100, 2);
        }
        if (null === $moneyPerMax) {
            $total = bcmul($moneyTotal, 100, 2);
            $moneyPerMax = max(intval($total / $number * 2), 1);
            $moneyPerMax = bcdiv($moneyPerMax, 100, 2);
        }

        $ret = self::money($moneyTotal, $number, $moneyPerMin, $moneyPerMax);
        if ($ret['code']) {
            return self::generate(-1, $ret['msg']);
        }
        return self::generate(0, null, ['money' => $ret['data']['moneys'][0]]);
    }

    /**
     * 抽取一个奖品
     *
     * @param array $pool = array(
     *  ['id'=>xx,'rate'=>5],
     *  ['id'=>xx,'rate'=>5],
     *  ['id'=>xx,'rate'=>5],
     * )
     * 按照百分数
     * @return int|null
     * @throws \Exception
     */
    public static function lottery(array $pool)
    {
        $map = [];
        $index = 0;
        foreach ($pool as $item) {
            for ($i = 0; $i < $item['rate']; $i++) {
                $map[$index++] = $item['id'];
            }
            if ($index > 100) {
                throw new \Exception('bad lottery pool');
            }
        }
        while ($index < 100) {
            $map[$index++] = null;
        }
        $index = rand(0, 99);
        return $map[$index];
    }

    /**
     * 抽取一个奖品
     *
     * @param array $pool = array(
     *  ['id'=>xx,'rate'=>5.01],
     *  ['id'=>xx,'rate'=>5.00],
     *  ['id'=>xx,'rate'=>5.00],
     * )
     * @return id or null
     * @throws \Exception
     */
    public static function preciseLottery(array $pool)
    {
        $map = [];
        $index = 0;
        foreach ($pool as $item) {
            $space = intval(bcmul($item['rate'], 100, 2));
            for ($i = 0; $i < $space; $i++) {
                $map[$index++] = $item['id'];
            }
            if ($index > 10000) {
                throw new \Exception('bad lottery pool 10000');
            }
        }
        while ($index < 10000) {
            $map[$index++] = null;
        }
        $index = rand(0, 9999);
        return $map[$index];
    }

    /**
     * 获取一个可能性
     *
     * @param $percent
     * @return bool
     */
    public static function probability($percent)
    {
        return rand(0, 99) < $percent;
    }
}
