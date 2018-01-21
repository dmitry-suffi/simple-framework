<?php

namespace suffi\Simple\Core;

use suffi\Simple\Core\Http\Session\Session;

/**
 * Класс-хелпер для быстрого доступа к компонентам
 * Class Simple
 * @package suffi\Simple\Core
 */
class Simple
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
    public static function logError(string $message, array $context = array())
    {
        self::getLogger()->error($message, $context);
    }

    /**
     * Запись отладки в лог
     * @param string $message
     * @return void
     */
    public static function debug(string $message, array $context = array())
    {
        self::getLogger()->debug($message, $context);
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

//    /**
//     * Компонент для работы с бд
//     * @return DB
//     */
//    public static function getDb()
//    {
//        return self::$app->getContainer()->get('DB');
//    }

    /**
     * Компонент для работы с сессиями
     * @return Session
     */
    public static function getSession()
    {
        return self::$app->getContainer()->get('Session');
    }
}
