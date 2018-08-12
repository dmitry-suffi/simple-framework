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

    public function log($level, $message, array $context = array())
    {
        syslog($level, $message . ' ' . print_r($context, true));
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        closelog();
    }
}
