<?php

namespace suffi\Simple\Core\Http\Session;

/**
 * Class RedisSession
 *
 * Класс для работы с сессиями через редис
 *
 * @package suffi\Simple\Core\Http\Session
 *
 * <pre>
 *     'Session' => [
 *          'class' => 'suffi\Simple\Core\Http\Session\RedisSession',
 *          'parameters' => [
 *              'name' => 'my_project'
 *          ]
 *      ],
 * </pre>
 */
class RedisSession extends Session
{
    public function __construct($name = 'PHPSESSID')
    {
        $redis = \suffi\Simple\Core\nc::get('Redis');
        $sessionHandler = ($redis instanceof \Redis && $redis->isConnect()) ? new \suffi\RedisSessionHandler\RedisSessionHandler($redis) : new \SessionHandler();

        session_set_save_handler($sessionHandler);
        register_shutdown_function(function () use ($sessionHandler) {
            $sessionHandler->close();
        });

        parent::__construct($name);
    }
}
