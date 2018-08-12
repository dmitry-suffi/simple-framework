<?php

namespace suffi\Simple\Components\Logger;

use suffi\Simple\Core\Logger;

/**
 * Класс для логирования в файл
 * Class FileLogger
 */
class FileLogger extends Logger
{

    /**
     * Запись
     * @param $message
     * @param string $fullMessage
     */
    private function write($message, $fullMessage = '')
    {
        $f = fopen('log/log.txt', 'a+');
        fwrite($f, "\r\n" . date('Y-m-d H:i:s') . ' ' . $message . "\r\n" . $fullMessage);
        fclose($f);
    }

    public function log($level, $message, array $context = array())
    {
        $this->write($level . ' ' . $message, $context ? print_r($context, true) : '');
    }
}
