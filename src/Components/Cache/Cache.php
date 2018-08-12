<?php

namespace suffi\Simple\Ext\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Cache
 *
 * Абстрактный класс для кэша по psr-7
 *
 * @package suffi\Simple\Ext\Cache
 */
abstract class Cache implements CacheItemPoolInterface
{
    /**
     * Получение элемента по ключу
     * @param string $key
     * @return mixed
     */
    abstract public function getItem($key);

    /**
     * Получение элементов по ключам
     * @param array $keys
     * @return mixed
     */
    abstract public function getItems(array $keys = array());

    /**
     * Проверка на наличие элемента по ключу
     * @param string $key
     * @return mixed
     */
    abstract public function hasItem($key);

    /**
     * Очищение кэша
     * @return mixed
     */
    abstract public function clear();

    /**
     * Удаление элемента по ключу
     * @param string $key
     * @return mixed
     */
    abstract public function deleteItem($key);

    /**
     * Удаление элементов по ключам
     * @param array $keys
     * @return mixed
     */
    abstract public function deleteItems(array $keys);

    /**
     * Сохранение элемента в кэш
     * @param CacheItemInterface $item
     * @return mixed
     */
    abstract public function save(CacheItemInterface $item);

    /**
     * Установка элемента в кэш для сохранения позже
     * @param CacheItemInterface $item
     * @return mixed
     */
    abstract public function saveDeferred(CacheItemInterface $item);

    /**
     * Сохранение
     * @return mixed
     */
    abstract public function commit();

    /**
     * Создание элемента с ключом
     * @param $key
     * @return CacheItem
     */
    public function newItem($key): CacheItem
    {
        return new CacheItem($key);
    }
}
