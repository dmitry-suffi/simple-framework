<?php

namespace suffi\Simple\Components\Mutex;

/**
 * Класс для механизма блокировок
 *
 * <pre>
 * if ($mutex->acquire($mutexName)) {
 *     // business logic execution
 * } else {
 *     // execution is blocked!
 * }
 * </pre>
 *
 * Class Mutex
 * @package suffi\Simple\Components\Mutex
 *
 * @see http://www.yiiframework.com/doc-2.0/yii-mutex-mutex.html
 */
abstract class Mutex
{
    /**
     * Имена ключей блокировок1
     * @var string[]
     */
    private $locks = [];

    /**
     * Конструктор
     */
    final public function __construct()
    {
        $locks = &$this->locks;
        register_shutdown_function(function () use (&$locks) {
            foreach ($locks as $lock) {
                $this->release($lock);
            }
        });
    }

    /**
     * Инициализация
     */
    public function init()
    {
    }

    /**
     * Захват блокировки
     * @param $name
     * @param int $timeout
     * @return bool
     */
    public function acquire($name, $timeout = 0)
    {
        if ($this->acquireLock($name, $timeout)) {
            $this->locks[] = $name;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Снятие блокировки
     * @param $name
     * @return bool
     */
    public function release($name)
    {
        if ($this->releaseLock($name)) {
            $index = array_search($name, $this->locks);
            if ($index !== false) {
                unset($this->locks[$index]);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Метод для реализации захвата блокировки
     * @param $name
     * @param int $timeout
     * @return mixed
     */
    abstract protected function acquireLock($name, $timeout = 0);

    /**
     * Метод для реализации снятия блокировки
     * @param $name
     * @return mixed
     */
    abstract protected function releaseLock($name);
}
