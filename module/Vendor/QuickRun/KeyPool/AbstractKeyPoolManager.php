<?php

namespace Module\Vendor\QuickRun\KeyPool;

abstract class AbstractKeyPoolManager
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    abstract public function loadItems();

    abstract public function saveItemCount($key);

    abstract public function saveItemLock($key, $available);

    abstract public function saveItemBan($key);

    abstract public function saveItemAdd($key, $item);

    abstract public function saveItemDelete($key);

    /**
     * @param array $updateItems
     */
    public function setItems($updateItems)
    {
        $items = $this->loadItems();
        $itemKeys = array_build($items, function ($k, $v) {
            return [$k, $k];
        });

        foreach ($updateItems as $k => $item) {
            unset($itemKeys[$k]);
            if (!isset($items[$k])) {
                $this->saveItemAdd($k, [
                    'item' => $item,
                    'status' => KeyPoolItemStatus::USING,
                    'count' => 0,
                    'available' => time(),
                ]);
            }
        }
        foreach ($itemKeys as $k => $v) {
            $this->saveItemDelete($k);
        }
    }

    public function itemGet()
    {
        $items = $this->loadItems();
        foreach ($items as $k => $item) {
            if ($item['status'] == KeyPoolItemStatus::USING && $item['available'] <= time()) {
                $this->saveItemCount($k);
                return [$k, $item['item']];
            }
        }
        return [null, null];
    }

    public function itemLock($key, $available)
    {
        $this->saveItemLock($key, $available);
    }

    public function itemBan($key)
    {
        $this->saveItemBan($key);
    }

    public function itemHasMore($currentKey)
    {
        $items = $this->loadItems();
        foreach ($items as $k => $item) {
            if ($item['status'] == KeyPoolItemStatus::USING && $item['available'] <= time() && $k != $currentKey) {
                return true;
            }
        }
        return false;
    }

}
