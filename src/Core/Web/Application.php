<?php

namespace suffi\Simple\Core\Web;


use suffi\Simple\Components\Cache\ArrayCache;
use suffi\Simple\Components\Logger\FakeLogger;
use suffi\Simple\Core\Http\Session\Session;


/**
 * Класс-приложение для web
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
        }
        if (!$this->getContainer()->hasDefinition('Request')) {
            $this->getContainer()->setDefinition('Request', Request::class)
                ->init('init')
            ;
        }
        if (!$this->getContainer()->hasDefinition('Logger')) {
            $this->getContainer()->setDefinition('Logger', FakeLogger::class);
        }
        if (!$this->getContainer()->hasDefinition('Cache')) {
            $this->getContainer()->setDefinition('Cache', ArrayCache::class);
        }
        if (!$this->getContainer()->hasDefinition('Session')) {
            $this->getContainer()->setDefinition('Session', Session::class);
        }
    }
}
