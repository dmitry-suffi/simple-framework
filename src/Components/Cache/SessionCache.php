<?php


namespace suffi\Simple\Ext\Cache;

use suffi\Simple\Core\Simple;
use Psr\Cache\CacheItemInterface;

class SessionCache extends Cache
{

    /**
     * Ключ сессии
     * @var string
     */
    protected static $sessionKey = 'cache';

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        return Simple::getSession()->get(self::$sessionKey . '.' . $key) ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = Simple::getSession()->get(self::$sessionKey . '.' . $key) ?? null;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return Simple::getSession()->has(self::$sessionKey . '.' . $key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        Simple::getSession()->remove(self::$sessionKey);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        Simple::getSession()->remove(self::$sessionKey . '.' . $key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $cache = Simple::getSession()->get(self::$sessionKey);
        foreach ($keys as $key) {
            unset($cache[$key]);
        }
        Simple::getSession()->set(self::$sessionKey, $cache);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        Simple::getSession()->set(self::$sessionKey . '.' . $item->getKey(), $item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        Simple::getSession()->set(self::$sessionKey . '.' . $item->getKey(), $item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }
}
