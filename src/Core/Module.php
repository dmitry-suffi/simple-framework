<?php

namespace suffi\Simple\Core;

use suffi\Simple\Core\di\ContainerTrait;
use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Helpers\ArrayHelper;

/**
 * Базовый класс модуля
 * Class Module
 * @package suffi\Simple\Core
 */
class Module
{
    use ContainerTrait;

    /**
     * Список контроллеров
     * @var array
     */
    protected $controllerMap = [];

    /**
     * Вьюшка
     * @var View
     */
    protected $view = null;

    /**
     * Имя
     * @var string
     */
    protected $name = '';

    /**
     * Название
     * @var string
     */
    protected $title = 'Module';

    /**
     * Папка шаблонов
     * @var string
     */
    protected $addTemplateDir = '';

    /**
     * Параметры
     * @var array
     */
    public $params = [];

    /**
     * Конфиги
     * @var array
     */
    public $config = [];

    /**
     * Директория модуля
     * @var string
     */
    protected $moduleDir = '';

    /**
     * Веб-директория модуля
     * @var string
     */
    protected $moduleWebDir = '';

    /**
     * Список консольных команд
     * @var array
     */
    public $consoleCommand = [];

    /**
     * Инициализация модуля
     */
    public function init()
    {
    }

    /**
     * Проверка на существование контроллера
     * @param $name
     * @return bool
     */
    public function hasController($name)
    {
        return isset($this->controllerMap[$name]);
    }

    /**
     * Получение параметра конфига.
     * @param string $paramName Название параметра
     * @param null $default Значение по умолчанию
     * @return mixed|null
     */
    public function getParam(string $paramName, $default = null)
    {
        return ArrayHelper::get($this->params, $paramName, $default);
    }

    /**
     * Имя
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Заголовок
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает контроллер по имени
     * @param $name
     * @throws ConfigException
     * @return Controller
     */
    public function getController($name)
    {
        $controllerName = $this->controllerMap[$name];
        $controller = new $controllerName();

        if (!$controller instanceof Controller) {
            throw new ConfigException();
        }
        $controller->setModule($this);
        return $controller;
    }

    /**
     * Вьюшка
     * @return View
     */
    public function getView():View
    {
        /** @var View $view */
        $view = $this->getContainer()->get('View');

        if ($this->addTemplateDir) {
            $view->addTemplateDir($this->addTemplateDir);
        }
        $view->addTemplateDir($this->moduleDir . DIRECTORY_SEPARATOR . 'Views');

        return $view;
    }

    /**
     * Статические файлы для вывода в шаблон
     * @return array
     */
    public function getStatic()
    {
        $data = [];

        if (isset($this->config['css'])) {
            foreach ($this->config['css'] as $group => $items) {
                foreach ($items as $item) {
                    $item = trim($item, '/');
                    $data[View::CSS][] = $this->getPath($group) . $item . '?v=' . nc::$app->getBuildVersion();
                }
            }
        }

        if (isset($this->config['js'])) {
            foreach ($this->config['js'] as $group => $items) {
                foreach ($items as $item) {
                    $item = trim($item, '/');
                    $data[View::JS][] = $this->getPath($group) . $item . '?v=' . nc::$app->getBuildVersion();
                }
            }
        }

        return $data;
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
        $this->config['css'][$group][] = $name;
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
        $this->config['js'][$group][] = $name;
    }

    /**
     * @param $group
     * @return string
     */
    private function getPath($group)
    {
        $staticPath = nc::$app->scriptUrl . '/Common/static/';

        if ($group == 'app') {
            $path = $staticPath;
        } elseif ($group == 'module') {
            $path = $this->moduleWebDir . '/static/';
        } else {
            $path = '';
        }
        return $path;
    }
}
