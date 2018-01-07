<?php

namespace suffi\Simple\Core\Http\Session;

use suffi\Simple\Helpers\ArrayHelper;

/**
 * Хранилище для сессий в массиве, без сохранения
 *
 * Class ArraySession
 * @package suffi\Simple\Core\Http\Session
 */
class ArraySession extends Session
{
    /**
     * Имя сессии
     * @var string
     */
    protected $name = '';

    /**
     * Id сессии
     * @var string
     */
    protected $id = '';

    /**
     * Хранилище данных
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    public function start():bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getId():string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function migrate($destroy = false):bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name):bool
    {
        return ArrayHelper::has($this->data, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return ArrayHelper::get($this->data, $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value)
    {
        ArrayHelper::set($this->data, $name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name)
    {
        ArrayHelper::remove($this->data, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];
    }
}
