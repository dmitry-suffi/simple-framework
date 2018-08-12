<?php

namespace suffi\Simple\Components\Logger;

use suffi\Simple\Core\Logger;

/**
 * Class SyslogLogger
 *
 * Класс для логирования в syslog
 */
class SyslogLogger extends Logger
{

    /**
     * ident
     * @var string
     */
    public $ident = '';

    public function __construct()
    {
        openlog($this->ident, LOG_CONS | LOG_ODELAY | LOG_PID | LOG_PERROR, LOG_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, $fullMessage = '')
    {
        syslog(LOG_DEBUG, $message . ' ' . $fullMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, $fullMessage = '')
    {
        syslog(LOG_ERR, $message . ' ' . $fullMessage);
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        closelog();
    }
}
