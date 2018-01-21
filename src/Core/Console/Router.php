<?php

namespace suffi\Simple\Core\Console;

use suffi\Simple\Core\Controller;
use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Core\Exceptions\NotFoundException;
use suffi\Simple\Core\Module;
use suffi\Simple\Core\Simple;

/**
 * Class Router
 * @package suffi\Simple\Core\Console
 */
class Router extends \suffi\Simple\Core\Router
{

    /**
     * Получение контроллера
     * @return Controller
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function getController()
    {
        list($moduleName, $controllerName) = $this->route();
        if ($moduleName && $controllerName) {
            return $this->createController($moduleName, $controllerName);
        } else {
            return new HelpController();
        }
    }

    /**
     * Получение названия модуля и контроллера
     * @return array
     */
    public function route():array
    {
        /** @var $request */
        $request = Simple::getRequest();

        $params = $request->getQueryParams();

        $route = $params[0] ?? '';
        if ($route) {
            if (preg_match('/(.+?)\/(.+?)\/(.+?)/', $route)) {
                list($moduleName, $controllerName, $this->action) = explode('/', $route);
            } else {
                list($moduleName, $controllerName, $this->action) = $params;
            }

            return array($moduleName, $controllerName);
        }

        return array('', '');

    }
}
