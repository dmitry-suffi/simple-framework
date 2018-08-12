<?php

namespace suffi\Simple\Components\DB;

/**
 * Class DB
 *
 * Базовый класс для работы с бд
 *
 * @package suffi\Simple\Components\DB
 */
abstract class DB
{

    /**
     * Выполнение запроса на выборку данных
     * @param string $query Текст SQL запроса
     * @param array $params Параметры запроса, ключ => значение
     * @param array $options Массив дополнительных настроек
     * @return mixed Возвращает массив данных или текст ошибки
     */
    abstract public function query($query, $params = [], $options = []);

    /**
     * Выполнение запроса
     * @param string $query Текст SQL запроса
     * @param array $params Параметры запроса, ключ => значение
     * @param array $options Массив дополнительных настроек
     * @return mixed Возвращает true или текст ошибки
     */
    abstract public function execute($query, $params = [], $options = []);

    /**
     * Выполнение запроса с возвратом значения
     * @param string $query Текст SQL запроса
     * @param string $label Параметр для возврата
     * @param array $params Параметры запроса, ключ => значение
     * @param array $options Массив дополнительных настроек
     * @return mixed Возвращает значение или текст ошибки
     */
    abstract public function executeWithResult($query, $label, $params = [], $options = []);
}
