<?php

namespace suffi\Simple\Core;

use suffi\Simple\Core\Exceptions\NotFoundException;

/**
 * Базовый класс контроллера
 * Class Controller
 * @package suffi\Simple\Core
 */
class Controller
{
    /**
     * Массив css
     * @var array
     */
    protected $css = [];

    /**
     * Массив js
     * @var array
     */
    protected $js = [];

    /**
     * Действие по умолчанию
     * @var string
     */
    protected $defaultAction = '';

    /**
     * Модуль контроллера
     * @var Module
     */
    protected $module = null;

    /**
     * Папка с шаблонами
     * @var string
     */
    protected $viewPath = '';

    /**
     * Модуль контроллера
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Установка модуля контроллера
     * @param Module $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Подготовка имени
     * @param $name
     * @return mixed
     */
    private function prepareName($name)
    {
        if ($name) {
            return implode('', array_map(function ($a) {
                return ucfirst($a);
            }, explode('-', $name)));
        }
        return $name;
    }

    /**
     * Запуск действия.
     * @param $action
     * @return mixed
     * @throws NotFoundException
     */
    final public function run($action)
    {
        if ($action) {
            $actionName = 'action' . $this->prepareName($action);
            if (method_exists($this, $actionName)) {
                $this->beforeAction($actionName);
                $run = $this->$actionName();
                $this->afterAction($actionName);
                return $run;
            }
        }

        $actionName = 'action' . $this->prepareName($this->defaultAction);
        if (method_exists($this, $actionName)) {
            $this->beforeAction($actionName);
            $run = $this->$actionName();
            $this->afterAction($actionName);
            return $run;
        }
        throw new NotFoundException('Action not found');
    }

    /**
     * Метод, вызываемый перед action
     * @param $actionName
     */
    public function beforeAction($actionName)
    {
    }

    /**
     * Метод, вызываемый после action
     * @param $actionName
     */
    public function afterAction($actionName)
    {
    }

    /**
     * Добавление css
     * @param string $name Название
     * @param string $group
     *              <ul>
     *                  <li>module - из папки static модуля, значение по умолчанию</li>
     *                  <li>app - из папки Common/static сборки</li>
     *              </ul>
     */
    public function addCss($name, $group = 'module')
    {
        $this->getModule()->addCss($name, $group);
    }

    /**
     * Добавление js
     * @param string $name Название
     * @param string $group
     *              <ul>
     *                  <li>module - из папки static модуля, значение по умолчанию</li>
     *                  <li>app - из папки Common/static сборки</li>
     *              </ul>
     */
    public function addJs($name, $group = 'module')
    {
        $this->getModule()->addJs($name, $group);
    }
    
    /**
     * Доступ к вьюшке
     * @return View
     */
    public function getView():View
    {
        return $this->module->getView();
    }

    /**
     * Рендеринг данныг в шаблон
     * @param $template
     * @param array $data
     */
    public function render($template, $data = [])
    {
        if ($this->viewPath != '') {
            $template = $this->viewPath . DIRECTORY_SEPARATOR . $template;
        }
        $view = $this->getView();

        $data['static'] = $this->getModule()->getStatic();

        $data['project'] = Simple::$app->getParam('project');

        $data['module_params'] = json_encode($this->module->params);

        $view->render($template, $data);
    }


    /**
     * Массив css
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Массив js
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }
}
