<?php

namespace suffi\Simple\Core;

use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Core\Exceptions\NotFoundException;

/**
 * Class Router
 *
 * Реализует роутинг
 *
 * Пример конфигурации:
 * <pre>
 * [
 *      'properties' => [
 *          'defaultModule' => 'Call'
 *          'defaultController' => 'Call'
 *      ]
 * ]
 * </pre>
 * @package suffi\Simple\Core
 */
abstract class Router
{

    /**
     * Модуль по-умолчанию
     * @var string
     */
    public $defaultModule = '';

    /**
     * Контроллер по-умолчанию
     * @var string
     */
    public $defaultController = '';

    /**
     * Действие
     * @var string
     */
    protected $action = '';

    /**
     * Действие
     * @return string
     */
    public function getAction()
    {
        if (!$this->action) {
            return Simple::getRequest()->post('action', '');
        }
        return $this->action;
    }

    /**
     * Получение контроллера
     * @return Controller
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function getController()
    {
        list($moduleName, $controllerName) = $this->route();

        return $this->createController($moduleName, $controllerName);
    }

    /**
     * Получение названия модуля и контроллера
     * @return array[$moduleName, $controllerName]
     */
    abstract protected function route(): array;

    /**
     * Создание контроллера
     * @param $moduleName
     * @param $controllerName
     * @return Controller
     * @throws ConfigException
     * @throws NotFoundException
     */
    protected function createController($moduleName, $controllerName): Controller
    {
        $moduleConfig = Simple::getParam('modules.' . $moduleName);
        if (!isset($moduleConfig['class']) || !$moduleConfig['class']) {
            throw new ConfigException("Не задана конфигурация для модуля " . $moduleName);
        }
        $moduleName = $moduleConfig['class'];

        $module = Simple::$app->getContainer()->has($moduleName)
            ? Simple::$app->getContainer()->get($moduleName)
            : new $moduleName();
        if (!$module instanceof Module || !$module) {
            throw new ConfigException("Не верно задана конфигурация для модуля " . $moduleName);
        }

        $module->getContainer()->setParentsContainer(Simple::$app->getContainer());
        $module->configure($moduleConfig['components'] ?? []);

        $module->params = $moduleConfig['params'] ?? [];

        $module->init();

        if ($module->hasController($controllerName)) {
            return $module->getController($controllerName);
        }

        throw new NotFoundException("Не верно задана конфигурация для модуля " . $moduleName);
    }
}
