<?php

namespace suffi\Simple\Ext\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * Class ArrayCache
 *
 * Релизация кэша через массив
 *
 * @package suffi\Simple\Ext\Cache
 */
class ArrayCache extends Cache
{
    /**
     * Хранилище
     *
     * @var array
     */
    protected $cache = [];

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = $this->cache[$key];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        unset($this->cache);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        unset($this->cache[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->cache[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $this->cache[$item->getKey()] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->cache[$item->getKey()] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }
}
