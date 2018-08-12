<?php

namespace suffi\Simple\Modules\System;

/**
 * Модуль для системных настроек
 *
 * Class Module
 * @package suffi\Simple\Modules\System
 */
class Module extends \suffi\Simple\Core\Module
{
    /**
     * Имя
     * @var string
     */
    protected $name = 'System';

    /**
     * Название
     * @var string
     */
    protected $title = 'Системный модуль';

    /**
     * Список контроллеров
     * @var array
     */
    protected $controllerMap = [
        'Cache' => 'suffi\Simple\Modules\System\CacheController',
        'System' => 'suffi\Simple\Modules\System\SystemController',
    ];

    /**
     * Список консольных команд
     * @var array
     */
    public $consoleCommand = [
        'System/Cache/clear' => 'Очистка кеша',
        'System/System/clear' => 'Очистка кеша шаблонизатора',
    ];

    public function __construct()
    {
        $dir = __DIR__;
        $this->moduleDir = rtrim($dir, DIRECTORY_SEPARATOR);
    }
}
