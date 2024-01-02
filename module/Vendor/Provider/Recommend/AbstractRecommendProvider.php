<?php


namespace Module\Vendor\Provider\Recommend;

abstract class AbstractRecommendProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * 推荐条目内容
     * @param $biz string 业务
     * @param $bizId int 条目ID
     * @param $sceneId int 场景ID
     * @param $tags array 标签
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok"
     * }
     */
    abstract public function itemUpdate($biz, $bizId, $sceneId = 0, $tags = [], $param = []);

    /**
     * 推荐条目删除
     * @param $biz string 业务
     * @param $bizId int 条目ID
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok"
     * }
     */
    abstract public function itemDelete($biz, $bizId, $param = []);


    /**
     * 推荐条目列表
     * @param $biz string 业务
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok"
     * }
     */
    abstract public function itemTrash($biz, $param = []);


    /**
     * 推荐条目数量
     * @param $biz string 业务
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *   "code": 0,
     *   "msg": "ok",
     *   "data": {
     *     "count": 100
     *   }
     * }
     */
    abstract public function itemCount($biz, $param = []);

    /**
     * 推荐条目内容反馈
     * @param $biz string 业务
     * @param $bizId int 条目ID
     * @param $userId int 用户ID
     * @param $type string 类型 RecommendUserFeedbackType
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok"
     * }
     */
    abstract public function itemFeedback($biz, $bizId, $userId, $type, $param = []);

    /**
     * 随机获取推荐条目
     * @param $biz string 业务
     * @param $userId int 用户ID
     * @param $limit int 条目数
     * @param $sceneIds array 场景ID，返回的内容必须包含这些场景ID
     * @param $tags array 标签，返回的内容可能包含这些标签
     * @param $exceptBizIds array 排除的条目ID
     * @param $param array 参数
     * @return array
     * @returnExample
     * {
     *  "code": 0,
     *  "msg": "ok",
     *  "data":{
     *    "bizIds": [1,2,3,4,5]
     *  }
     * }
     */
    abstract public function randomItem($biz, $userId, $limit = 1, $sceneIds = [], $tags = [], $exceptBizIds = [], $param = []);

}
