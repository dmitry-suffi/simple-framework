<?php

namespace suffi\Simple\Ext\Redis;

/**
 * Class Redis
 * @package suffi\Simple\Ext\Redis
 *
 * <pre>
 *     'Redis' => [
 *          'class' => 'suffi\Simple\Ext\Redis\Redis',
 *          'init' => 'init',
 *          'properties' => [
 *              'host' => '',
 *              'database' => 300
 *          ]
 *      ]
 * </pre>
 */
class Redis extends \Redis
{
    /**
     * База
     * @var int
     */
    public $database = 0;

    /**
     * Хост
     * @var string
     */
    public $host = '';

    /**
     * Порт
     * @var int
     */
    public $port = 6379;

    /**
     * Таймаут соединения
     * @var float
     */
    public $timeout = 0.0;

    private $isConnect = false;

    /**
     * Хост
     * @return string
     */
    public function getHost()
    {
        if (!$this->host) {

            if (ini_get('session.save_handler') == 'redis') {
                $savePath = ini_get('session.save_path');
                if (substr($savePath, 0, 5) === 'tcp:/') {
                    $url = parse_url($savePath);
                    $this->host = $url['host'] ?? '';
                    $this->port = $url['port'] ?? $this->port;
                } else {
                    $this->host = $savePath;
                }

            }
        }
        return $this->host;
    }

    /**
     * Флаг активности соединения
     * @return bool
     */
    public function isConnect()
    {
        return $this->isConnect;
    }

    /**
     * Инициализация
     */
    public function init()
    {
        $i = 0;
        do {
            $error = false;
            try {
                $this->isConnect = $this->connect($this->getHost(), $this->port, $this->timeout);

                if (!$this->isConnect) {
                    throw new \RedisException('Нет соединения с сервером Redis');
                }

                $select = $this->select($this->database);
                if (!$select) {
                    throw new \RedisException('Нет соединения с бд Redis');
                }

            }
            catch (\RedisException $e) {
                $i++;
                $error = true;
                usleep(50);
            }
        }
        while($error && $i < 5);

        $this->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }
}
