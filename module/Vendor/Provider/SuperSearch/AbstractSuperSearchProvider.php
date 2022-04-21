<?php


namespace Module\Vendor\Provider\SuperSearch;


abstract class AbstractSuperSearchProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function ping();

    abstract public function bucketExists($name);

    abstract public function bucketCreate($name, $fields);

    abstract public function bucketDelete($name);

    abstract public function bucketCount($name);

    abstract public function upsert($name, $id, $data);

    abstract public function delete($name, $id);

    abstract public function get($name, $id);

    abstract public function search($name, $page, $pageSize, $query = [], $order = []);

    public function ensureBucket($name)
    {
        if (!$this->bucketExists($name)) {
            $biz = SuperSearchBiz::get($name);
            $this->bucketCreate($name, $biz->fields());
        }
    }
}
