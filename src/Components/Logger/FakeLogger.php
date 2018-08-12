<?php

namespace suffi\Simple\Components\Logger;

use suffi\Simple\Core\Logger;

/**
 * Заглушка для выключения логирования
 * Class FakeLogger
 */
class FakeLogger extends Logger
{
    public function log($level, $message, array $context = array())
    {
        return;
    }
}
