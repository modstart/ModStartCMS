<?php


namespace Module\Vendor\Provider\LazyValue;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use Module\Vendor\Model\LazyValue;
use Module\Vendor\Provider\BizTrait;

/**
 * @method static AbstractLazyValueBiz[] listAll()
 * @method static AbstractLazyValueBiz getByName($name)
 */
class LazyValueBiz
{
    use BizTrait;

    public static function dispatch($biz, $param)
    {
        $job = new LazyValueJob();
        $job->key = $biz;
        $job->param = $param;
        $job->onQueue('LazyValue');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function dispatchRefresh($biz, $param)
    {
        $job = new LazyValueJob();
        $job->key = $biz;
        $job->param = $param;
        $job->onQueue('LazyValueRefresh');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function get($biz, $param = [], $expireLife = 86400)
    {
        $bizer = self::getByName($biz);
        BizException::throwsIfEmpty("BizNotFound", $bizer);
        $where = [
            'key' => $bizer->name(),
            'param' => SerializeUtil::jsonEncode($param)
        ];
        $record = ModelUtil::get(LazyValue::class, $where);
        if (empty($record)) {
            ModelUtil::insert(LazyValue::class, array_merge([
                'expire' => time() + $bizer->cacheSeconds(),
                'lifeExpire' => time() + $expireLife,
                'cacheSeconds' => $bizer->cacheSeconds(),
                'value' => SerializeUtil::jsonEncode($bizer->defaultValue()),
            ], $where));
            LazyValueBiz::dispatch($bizer->name(), $param);
            return [
                'status' => 'running',
                'value' => $bizer->defaultValue(),
            ];
        }
        if ($record['expire'] < time()) {
            LazyValueBiz::dispatchRefresh($bizer->name(), $param);
        }
        $value = @json_decode($record['value'], true);
        if (empty($value)) {
            return [
                'status' => 'running',
                'value' => $bizer->defaultValue(),
            ];
        }
        return [
            'status' => 'finish',
            'value' => $value,
        ];
    }

    private static function status(&$value)
    {
        foreach ($value as $v) {
            if (null === $v) {
                return 'running';
            }
        }
        return 'finish';
    }

    public static function fetch($bizParamList = [])
    {
        $status = 'finish';
        $value = [];
        foreach ($bizParamList as $k => $v) {
            if (!isset($v['param'])) {
                $v['param'] = [];
            }
            $ret = LazyValueBiz::get($v['biz'], $v['param']);
            if ($ret['status'] == 'running') {
                $status = 'running';
            }
            $value[$k] = $ret;
        }
        return Response::generate(0, 'ok', [
            'status' => $status,
            'value' => $value,
        ]);
    }
}
