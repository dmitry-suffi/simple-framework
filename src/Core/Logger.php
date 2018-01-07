<?php

namespace suffi\Simple\Core;

/**
 * Класс для логирования
 * Class Logger
 */
abstract class Logger
{

    /**
     * Запись отладки
     * @param $message
     * @param $fullMessage
     */
    abstract public function debug($message, $fullMessage = '');

    /**
     * Запись ошибки
     * @param $message
     * @param $fullMessage
     */
    abstract public function error($message, $fullMessage = '');
}
