<?php


namespace Module\Vendor\Provider\LazyValue;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\SerializeUtil;
use Module\Vendor\Model\LazyValue;

class LazyValueJob extends BaseJob
{
    public $key;
    public $param;

    public static function create($key, $param)
    {
        $job = new static($key, $param);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        if (null === $this->param) {
            $this->param = [];
        }
        Log::info("Vendor.LazyValue - Start - {$this->key}." . SerializeUtil::jsonEncode($this->param));
        $bizer = LazyValueBiz::getByName($this->key);
        if (empty($bizer)) {
            Log::info("Vendor.LazyValue - Error.BizNotFound");
            return;
        }
        $ret = $bizer->execute($this->param);
        if (Response::isError($ret)) {
            Log::info("Vendor.LazyValue - Error.BizExecuteError - " . SerializeUtil::jsonEncode($ret));
            return;
        }
        $value = $ret['data'];
        Log::info("Vendor.LazyValue - Value - " . SerializeUtil::jsonEncode($value));
        $where = [
            'key' => $this->key,
            'param' => SerializeUtil::jsonEncode($this->param),
        ];
        $first = ModelUtil::get(LazyValue::class, $where);
        if (empty($first)) {
            Log::info("Vendor.LazyValue - Error.RecordNotFound");
            return;
        }
        ModelUtil::update(LazyValue::class, $first['id'], [
            'expire' => time() + $bizer->cacheSeconds(),
            'value' => SerializeUtil::jsonEncode($value),
        ]);
        Log::info("Vendor.LazyValue - End");
    }
}
