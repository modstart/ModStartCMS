<?php


namespace Module\Vendor\Schedule;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Data\AbstractDataStorage;
use ModStart\Data\DataManager;
use Module\Vendor\Provider\Schedule\AbstractScheduleBiz;

class DataTempCleanScheduleBiz extends AbstractScheduleBiz
{
    public function cron()
    {
        return $this->cronEveryHour();
    }

    public function title()
    {
        return 'data_temp 文件自动清理';
    }

    public function run()
    {
        $expiredRecords = ModelUtil::model('data_temp')
            ->where('created_at', '<', date('Y-m-d H:i:s', time() - TimeUtil::PERIOD_DAY))
            ->limit(100)
            ->get(['category', 'path'])->toArray();
        foreach ($expiredRecords as $record) {
            $path = AbstractDataStorage::DATA_TEMP . '/' . $record['category'] . '/' . $record['path'];
            try {
                DataManager::deleteDataTempByPath($path);
            } catch (\Exception $e) {
                Log::info('Vendor.DataTempCleanScheduleBiz.DeleteFail - ' . $path . ' - ' . $e->getMessage());
                continue;
            }
            Log::info('Vendor.DataTempCleanScheduleBiz - ' . $path . ' deleted');
        }
    }

}
