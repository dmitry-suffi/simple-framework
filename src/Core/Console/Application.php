<?php

namespace suffi\Simple\Core\Console;

use suffi\Simple\Core\Http\Session\ArraySession;
use suffi\Simple\Components\Cache\ArrayCache;
use suffi\Simple\Components\Logger\FakeLogger;

/**
 * Класс-приложение для консоли
 * Class Application
 * @package suffi\Simple\Core
 */
class Application extends \suffi\Simple\Core\Application
{

    /**
     * Конфигурация контейнера поумолчанию
     */
    protected function defaultConfigure()
    {
        if (!$this->getContainer()->hasDefinition('Router')) {
            $this->getContainer()->setDefinition('Router', Router::class);
        } else {
            $this->getContainer()->removeDefinition('Router');
            $this->getContainer()->setDefinition('Router', Router::class);
        }

        if (!$this->getContainer()->hasDefinition('Request')) {
            $this->getContainer()->setDefinition('Request', Request::class)
                ->init('init');
        }
        if (!$this->getContainer()->hasDefinition('Logger')) {
            $this->getContainer()->setDefinition('Logger', FakeLogger::class);
        }
        if (!$this->getContainer()->hasDefinition('Cache')) {
            $this->getContainer()->setDefinition('Cache', ArrayCache::class);
        }
        if (!$this->getContainer()->hasDefinition('Session')) {
            $this->getContainer()->setDefinition('Session', ArraySession::class);
        }
    }
}
