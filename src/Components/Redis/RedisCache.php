<?php


namespace suffi\Simple\Ext\Redis;

use suffi\Simple\Ext\Cache\Cache;
use Psr\Cache\CacheItemInterface;

/**
 * Class RedisCache
 * @package suffi\Simple\Ext\Redis
 *
 * Кэш в Redis
 *
 * <pre>
 *     'Cache' => [
 *          'class' => 'suffi\Simple\Ext\Redis\RedisCache'
 *      ],
 * </pre>
 */
class RedisCache extends Cache
{
    /**
     * Объект для работы с Redis
     * @var \Redis
     */
    protected $redis = null;

    /**
     * Ключ сессии
     * @var string
     */
    protected static $sessionKey = 'cache';

    /**
     * Конструктор
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        return $this->redis->hGet(self::$sessionKey, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = array())
    {
        return $this->redis->hMGet(self::$sessionKey, $keys);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return $this->redis->hExists(self::$sessionKey, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $script = <<<LUA
        local hkeys = redis.call("HKEYS", KEYS[1])
        
        for k,v in ipairs(hkeys) do
            redis.call("HDEL", KEYS[1], v)
        end
        
        if redis.call("HLEN", KEYS[1]) > 0 then
            return 0
        else 
            return 1
        end
LUA;

        return $this->redis->eval($script, array(self::$sessionKey), 1);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        return $this->redis->hDel(self::$sessionKey, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $script = <<<LUA
        local sum = 0
        
        for k,v in ipairs(KEYS) do
            if k > 1 then 
                if redis.call("HDEL", KEYS[1], v) then
                    sum = sum + 1
                end
            end
        end
        
        return sum
LUA;

        array_unshift($keys, self::$sessionKey);
        return $this->redis->eval($script, $keys, count($keys));
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        return $this->redis->hSet(self::$sessionKey, $item->getKey(), $item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->redis->hSet(self::$sessionKey, $item->getKey(), $item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }
}
