<?php

namespace Module\Vendor\QuickRun\KeyPool;

class ConfigKeyPoolManager extends AbstractKeyPoolManager
{
    public function loadItems()
    {
        return modstart_config('KeyPool_' . $this->key, []);
    }

    private function saveItems($items)
    {
        modstart_config()->setArray('KeyPool_' . $this->key, $items);
    }

    public function saveItemCount($key)
    {
        $items = $this->loadItems();
        if (isset($items[$key])) {
            $items[$key]['count'] = $items[$key]['count'] + 1;
            $this->saveItems($items);
        }
    }

    public function saveItemLock($key, $available)
    {
        $items = $this->loadItems();
        if (isset($items[$key])) {
            $items[$key]['status'] = KeyPoolItemStatus::USING;
            $items[$key]['available'] = $available;
            $this->saveItems($items);
        }
    }

    public function saveItemBan($key)
    {
        $items = $this->loadItems();
        if (isset($items[$key])) {
            $items[$key]['status'] = KeyPoolItemStatus::BAN;
            $this->saveItems($items);
        }
    }

    public function saveItemAdd($key, $item)
    {
        $items = $this->loadItems();
        $items[$key] = $item;
        $this->saveItems($items);
    }

    public function saveItemDelete($key)
    {
        $items = $this->loadItems();
        unset($items[$key]);
        $this->saveItems($items);
    }
}
