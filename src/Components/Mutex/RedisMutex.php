<?php

namespace suffi\Simple\Components\Mutex;

/**
 * Класс для механизма блокировок через Redis
 *
 * Class RedisMutex
 * @package suffi\Simple\Components\Mutex
 *
 * <pre>
 * 'Mutex' => [
 *     'class' => 'suffi\Simple\Components\Mutex\RedisMutex',
 *     'init' => 'init',
 *     'setters' => [
 *         'redis' => 'Redis'
 *     ]
 * ]
 *
 * </pre>
 */
class RedisMutex extends Mutex
{
    /**
     * Продолжительность
     * @var int
     */
    public $expire = 30;

    /**
     * Префикс
     * @var string
     */
    protected $prefix;

    /**
     * Объект для работы с Redis
     * @var \Redis
     */
    public $redis = null;

    /**
     * Список значений параметров блокировки
     * @var array
     */
    private $lockValues = [];

    /**
     * Максимальное время ожидания разблокировки
     * @var int
     */
    private $lockMaxWait;

    /**
     * Время между попытками разблокировки
     * @var int
     */
    private $spinLockWait;

    /**
     * Объект для работы с Redis
     * @param \Redis $redis
     */
    public function setRedis(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $iniMaxExecutionTime = ini_get('max_execution_time');
        $this->lockMaxWait = $iniMaxExecutionTime ? $iniMaxExecutionTime * 0.7 : 5;

        $this->lockMaxWait = min($this->lockMaxWait, 2);
        $this->spinLockWait = 200000;
    }


    /**
     * {@inheritdoc}
     */
    protected function acquireLock($name, $timeout = 0)
    {
        $attempts = (1000000 * $this->lockMaxWait) / $this->spinLockWait;

        $key = $this->calculateKey($name);
        $value = uniqid();

        for ($i = 0; $i < $attempts; ++$i) {
            $success = $this->redis->set($key, $value, ['NX', (int)($this->expire * 1000) . 'PX']);
            if ($success) {
                $this->lockValues[$name] = $value;
                return true;
            }
            usleep($this->spinLockWait);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function releaseLock($name)
    {
        static $releaseLuaScript = <<<LUA
if redis.call("GET",KEYS[1])==ARGV[1] then
    return redis.call("DEL",KEYS[1])
else
    return 0
end
LUA;

        if (!isset($this->lockValues[$name]) || !$this->redis->eval($releaseLuaScript, [
                $this->calculateKey($name),
                $this->redis->_serialize($this->lockValues[$name])
            ], 1)
        ) {
            return false;
        } else {
            unset($this->lockValues[$name]);
            return true;
        }
    }

    /**
     * Подготовка ключа
     * @param $key
     * @return string
     */
    protected function calculateKey($key)
    {
        if (empty($this->prefix)) {
            return $key;
        }
        return $this->prefix . $key;
    }
}
