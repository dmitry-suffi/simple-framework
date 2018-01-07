<?php

namespace suffi\Simple\Core;

/**
 * Class View
 *
 * Класс для реализации уровня представления
 *
 * @package suffi\Simple\Core
 */
abstract class View
{
    const CSS = 'CSS';
    const JS = 'JS';

    /**
     * Отображение шаблона
     * @param string $template Название шаблона
     * @param array $data Данные для передачи в шаблон
     * @return mixed
     */
    abstract public function render($template, $data = []);

    /**
     * Добавление дополнительных директорий с шаблонами
     * @param $addTemplateDir
     * @return mixed
     */
    abstract public function addTemplateDir($addTemplateDir);

    /**
     * Получение директории с шаблоном
     * @param $index
     * @return mixed
     */
    abstract public function getTemplateDir($index);

    /**
     * Очистка временных данных
     */
    public function clear()
    {
    }
}
