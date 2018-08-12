<?php

namespace suffi\Simple\Core\Web;

use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Core\Exceptions\NotFoundException;
use suffi\Simple\Core\Simple;

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
class Router extends \suffi\Simple\Core\Router
{
    const ROUTE_PARAMS = 'route';

    /**
     * Получение названия модуля и контроллера
     * @return array
     */
    public function route(): array
    {
        /** @var $request */
        $request = Simple::getRequest();

        $route = $request->get(self::ROUTE_PARAMS, $request->post(self::ROUTE_PARAMS));
        if ($route && preg_match('/(.+?)\/(.+?)\/(.+?)/', $route)) {
            list($moduleName, $controllerName, $this->action) = explode('/', $route);
        } else {
            $moduleName = $request->get('module', $request->post('module', $this->defaultModule));
            $controllerName = $request->get('controller', $request->post('controller', $this->defaultController));
            $this->action = $request->get('action', $request->post('action'));
        }
        return array($moduleName, $controllerName);
    }
}
