<?php

namespace suffi\Simple\Core\Http\Session;

use suffi\Simple\Helpers\ArrayHelper;

/**
 * Class Session
 *
 * Класс для работы с сессиями
 *
 * @package suffi\Simple\Core\Http\Session
 *
 * <pre>
 *     'Session' => [
 *          'class' => 'suffi\Simple\Core\Http\Session\Session',
 *          'parameters' => [
 *              'name' => 'my_project'
 *          ]
 *      ],
 *
 * </pre>
 */
class Session
{

    /**
     * Флаг старта сессии
     * @var bool
     */
    private $started = false;

    /**
     * Конструктор.
     * @param string $name
     */
    public function __construct($name = 'PHPSESSID')
    {
        $this->setName($name);
        $this->start();
    }

    /**
     * Старт сессии
     * @return bool
     */
    public function start():bool
    {
        if ($this->started) {
            return true;
        }

        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }
        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }
        // ok to try and start the session
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session');
        }

        return true;
    }

    /**
     * Флаг запуска сессии
     * Возвращает true, если сессия запущена
     * @return bool
     */
    public function isStarted():bool
    {
        return $this->started;
    }

    /**
     * Получение id сессии
     * @return string
     */
    public function getId():string
    {
        return session_id();
    }

    /**
     * Установка id сессии
     * @param string $id
     */
    public function setId($id)
    {
        if ($this->isStarted()) {
            throw new \LogicException('Cannot change the ID of an active session');
        }

        session_id($id);
    }

    /**
     * Получение имени сессии
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Установка имени сессии
     * @param string $name
     */
    public function setName($name)
    {
        if ($this->isStarted()) {
            throw new \LogicException('Cannot change the name of an active session');
        }

        session_name($name);
    }

    /**
     * Регенерация идентификатор сеанса
     * @return bool
     */
    public function migrate($destroy = false):bool
    {
        // Cannot regenerate the session ID for non-active sessions.
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            return false;
        }

        $isRegenerated = session_regenerate_id($destroy);

        //@TODO TEST
        // The reference to $_SESSION in session bags is lost in PHP7 and we need to re-create it.
        // @see https://bugs.php.net/bug.php?id=70013

        return $isRegenerated;
    }

    /**
     * Сохранение
     */
    public function save()
    {
        session_write_close();

        $this->started = false;
    }

    /**
     * Проверка на наличие данных в сессии с ключом $name
     * @param string $name
     * @return bool
     */
    public function has(string $name):bool
    {
        return ArrayHelper::has($_SESSION, $name);
    }

    /**
     * Получение данных из сессии с ключом $name
     * @param string $name
     * @param mixed|null $default Значение, которое будет возвращаться, если в сессии нет данных с ключом $name
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return ArrayHelper::get($_SESSION, $name, $default);
    }

    /**
     * Запись в сессию данных $value с ключом $name
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        ArrayHelper::set($_SESSION, $name, $value);
    }

    /**
     * Получение всех данных сессии
     * @return mixed
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * Удаление данных из сессии с ключом $name
     * @param string $name
     */
    public function remove(string $name)
    {
        ArrayHelper::remove($_SESSION, $name);
    }

    /**
     * Очистка всех данных сессии
     */
    public function clear()
    {
        session_unset();
    }
}
