<?php

namespace suffi\Simple\Core;

use suffi\Simple\Core\Http\Session\Session;
use suffi\Simple\Ext\DB\DB;

/**
 * Класс-хелпер для быстрого доступа к компонентам
 * Class nc
 * @package suffi\Simple\Core
 */
class nc
{
    /**
     * Приложение
     * @var Application
     */
    public static $app = null;

    /**
     * Приложение
     * @return Application
     */
    public static function getApp()
    {
        return self::$app;
    }

    /**
     * Запись в лог об ошибке
     * @param string $message
     * @return void
     */
    public static function logError(string $message)
    {
        self::getLogger()->error($message);
    }

    /**
     * Запись отладки в лог
     * @param string $message
     * @return void
     */
    public static function debug(string $message)
    {
        self::getLogger()->debug($message);
    }

    /**
     * Логгер
     * @return Logger
     */
    public static function getLogger() : Logger
    {
        return self::get('Logger');
    }

    /**
     * Получение объекта из контейнера по ключу
     * @param $key
     * @return false|object
     */
    public static function get($key)
    {
        return self::$app->getContainer()->get($key);
    }

    /**
     * Реквест
     * @return Request
     */
    public static function getRequest()
    {
        return self::$app->getContainer()->get('Request');
    }

    /**
     * Получение параметра приложения
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public static function getParam($name, $default = null)
    {
        return self::getApp()->getParam($name, $default);
    }

    /**
     * Компонент для работы с бд
     * @return DB
     */
    public static function getDb()
    {
        return self::$app->getContainer()->get('DB');
    }

    /**
     * Компонент для работы с сессиями
     * @return Session
     */
    public static function getSession()
    {
        return self::$app->getContainer()->get('Session');
    }
}
