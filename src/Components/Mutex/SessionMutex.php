<?php

namespace suffi\Simple\Ext\Mutex;

use suffi\Simple\Core\Simple;

class SessionMutex extends Mutex
{
    static $sessionKey = 'Mutex';

    protected function acquireLock($name, $timeout = 0)
    {
        if (Simple::getSession()->has(self::$sessionKey . $name)) {
            return false;
        } else {
            Simple::getSession()->set(self::$sessionKey . $name, 'lock');
            return true;
        }
    }

    protected function releaseLock($name)
    {
        if (!Simple::getSession()->has(self::$sessionKey . $name)) {
            return false;
        } else {
            Simple::getSession()->remove(self::$sessionKey . $name);
            return true;
        }
    }
}