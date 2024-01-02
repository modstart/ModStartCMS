<?php


namespace Module\Vendor\Provider\Recommend;

abstract class AbstractRecommendBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function providerName();

    /**
     * 批量彤彤股
     * @param $nextId int 下一个ID
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok"
     *  "data": {
     *    "nextId": 0,
     *    "records:[
     *      {
     *        "biz": "cms",
     *        "bizId": 1,
     *        "sceneId": 1,
     *        "tags": ["tag1", "tag2"],
     *        "param": {}
     *      }
     *    ]
     * }
     */
    abstract public function syncBatch($nextId, $param = []);

}
