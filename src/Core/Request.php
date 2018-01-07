<?php

namespace suffi\Simple\Core;

/**
 * Класс для доступа к данным http-запроса
 * Class Request
 * @package suffi\Simple\Core
 */
abstract class Request
{
    /**
     * Параметры запроса
     * @var array
     */
    protected $queryParams = [];

    /**
     * Параметры тела запроса
     * @var array
     */
    protected $bodyParams = [];

    /**
     * Иниуиализация
     * @return mixed
     */
    abstract public function init();

    /**
     * Параметр запросак
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->queryParams[$name] ?? $default;
    }

    /**
     * Параметр post
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function post($name, $default = null)
    {
        return $this->bodyParams[$name] ?? $default;
    }

    /**
     * Post
     * @return array
     */
    public function getBodyParams()
    {
        return $this->bodyParams;
    }

    /**
     * Get
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * ip
     * @return mixed
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}
