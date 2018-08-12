<?php

namespace suffi\Simple\Ext\Mutex;

use suffi\Simple\Ext\DB\DB;

/**
 * Класс для механизма блокировок через Oracle
 * Требует грантов на DBMS_LOCK
 *
 * Class OracleMutex
 * @package suffi\Simple\Ext\Mutex
 *
 * <pre>
 * 'Mutex' => [
 *     'class' => 'suffi\Simple\Ext\Mutex\OracleMutex',
 *     'init' => 'init',
 *     'setters' => [
 *         'db' => 'DB'
 *     ]
 * ]
 *
 * </pre>
 */
class OracleMutex extends Mutex
{

    /**
     * Объект для работы с бд
     * @var DB
     */
    public $db = null;

    /**
     * Объект для работы с бд
     * @param DB $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function acquireLock($name, $timeout = 0)
    {
        $lockStatus = null;

        $timeout = abs((int)$timeout);

        $this->db->executeWithResult(
            'DECLARE
                handle VARCHAR2(128);
            BEGIN
                DBMS_LOCK.ALLOCATE_UNIQUE(:name, handle);
                :lockStatus := DBMS_LOCK.REQUEST(handle, DBMS_LOCK.X_MODE, :timeout, FALSE);
            END;',
            [
                ':name' => $name,
                ':timeout' => $timeout,
                ':lockStatus' => $lockStatus
            ]
        );

        return $lockStatus === 0 || $lockStatus === '0';
    }

    /**
     * {@inheritdoc}
     */
    protected function releaseLock($name)
    {
        $result = null;
        $this->db->executeWithResult(
            'DECLARE
                handle VARCHAR2(128);
            BEGIN
                DBMS_LOCK.ALLOCATE_UNIQUE(:name, handle);
                :result := DBMS_LOCK.RELEASE(handle);
            END;',
            [
                ':name' => $name,
                ':result' => $result
            ]
        );

        return $result === 0 || $result === '0';
    }
}
