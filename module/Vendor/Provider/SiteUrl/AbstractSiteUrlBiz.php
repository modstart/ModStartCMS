<?php


namespace Module\Vendor\Provider\SiteUrl;

/**
 * 链接生产者，比如XXX模块，可以提供链接
 */
abstract class AbstractSiteUrlBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function urlBuildBatch($nextId, $param = []);
    // {
    //     $records = [];
    //     $batchRet = ModelUtil::batch('xxx', $nextId, 1000, ['status'=>2]);
    //     $finish = empty($batchRet['records']);
    //     foreach ($batchRet['records'] as $record) {
    //         $records[] = [
    //             'url' => modstart_web_url('xxx/' . $record['id']),
    //             'updateTime' => $record['updated_at'],
    //         ];
    //     }
    //     return [
    //         'finish' => $finish,
    //         'records' => $records,
    //         'nextId'=>$batchRet['nextId'],
    //     ];
    // }
}
