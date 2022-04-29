<?php


namespace Module\Vendor\Provider\SuperSearch;


abstract class AbstractSuperSearchBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function fields();

    public function syncBatch(AbstractSuperSearchProvider $provider, $nextId)
    {
        $records = [];
        return [
            'count' => count($records),
            'nextId' => $nextId
        ];
    }
}
