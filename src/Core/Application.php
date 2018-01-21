<?php

namespace suffi\Simple\Core;

use suffi\di\ContainerTrait;
use suffi\ErrorHandler\ErrorHandler;
use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Helpers\ArrayHelper;

/**
 * Класс-приложение
 * Class Application
 * @package suffi\Simple\Core
 */
class Application
{
    use ContainerTrait;

    /**
     * Флаг использования обработчика ошибок
     * @var bool
     */
    public $useErrorHandler = true;

    /**
     * Путь к папке с приложением
     * @var string
     */
    protected $appDir = '';

    /**
     * Путь к папке со сборкой
     * @var string
     */
    protected $ncDir = '';

    /**
     * Относительный url папки со сборкой
     * @var string
     */
    public $scriptUrl = '';

    /**
     * Версия сборки
     * @var null|int
     */
    private $buildVersion = null;

    /**
     * Псевдонимы для di-контейнера
     * @var array
     */
    protected $_aliases = [
        'suffi\Simple\Ext\DB\DB' => 'DB',
        'suffi\Simple\Ext\Cache\Cache' => 'Cache',
        'suffi\Simple\Core\Router' => 'Router',
        'suffi\Simple\Core\View' => 'View',
        'suffi\Simple\Core\Request' => 'Request',
        'suffi\Simple\Core\Logger' => 'Logger',
        'suffi\Simple\Ext\Session' => 'Session',
    ];

    /**
     * Роутер
     * @var Router
     */
    protected $router = null;

    /**
     * Конфиг
     * @var array
     */
    private $config = [];

    /**
     * Конструктор
     */
    public function __construct()
    {
        Simple::$app = $this;

        $this->ncDir = dirname(__DIR__);
    }

    /**
     * Путь к папке с приложением
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Установка пути к папке с приложением
     * @param string $appDir
     */
    public function setAppDir($appDir)
    {
        $this->appDir = $appDir;
    }

    /**
     * Путь к папке со сборкой
     * @return string
     */
    public function getNcDir()
    {
        return $this->ncDir;
    }

    /**
     * Относительный url папки со сборкой
     * @return string
     */
    public function getScriptUrl()
    {
        return $this->scriptUrl;
    }

    /**
     *  Инициализация
     * @param array $config Массив конфигурации
     */
    public function init(array $config = [])
    {
        $baseConfig = $this->getBaseConfig();

        $config = ArrayHelper::merge($baseConfig, $config);

        $this->config = $config['params'] ?? [];
        $this->configure($config['components'] ?? []);

        $this->defaultConfigure();

        if ($this->useErrorHandler) {
            $this->initErrorHandler();
        }
    }

    /**
     * Получение параметра конфига.
     * @param string $paramName Название параметра
     * @param null $default Значение по умолчанию
     * @return mixed|null
     */
    public function getParam(string $paramName, $default = null)
    {
        return ArrayHelper::get($this->config, $paramName, $default);
    }

    /**
     * Версия сборки для версионности статики
     * @return mixed|null
     */
    public function getBuildVersion()
    {
        if (!is_null($this->buildVersion)) {
            return $this->buildVersion;
        }

        $debug = $this->getParam('debug', 0);

        if ($debug) {
            $this->buildVersion =  round(microtime(1));
        } else {
            $buildVersion = $this->getParam('build_version');
            if (!$buildVersion && defined('BUILD_VERSION')) {
                $buildVersion = BUILD_VERSION;
            }
            if ($buildVersion == '{BUILD_VERSION}') {
                $buildVersion =  round(microtime(1));
            }
            $this->buildVersion = $buildVersion;
        }

        return $this->buildVersion;
    }

    /**
     * Запуск
     * @throws ConfigException
     */
    final public function run()
    {
        $this->router = $this->getContainer()->get('Router');

        if (!$this->router) {
            throw new ConfigException();
        }

        $this->beforeHandle();
        $this->handle();

        $this->afterHandle();
    }

    /**
     * Выполнение
     */
    protected function handle()
    {
        $controller = $this->router->getController();

        echo $controller->run(Simple::getRequest()->get('action', $this->router->getAction()));
    }

    /**
     * Действие до выполнения
     */
    protected function beforeHandle()
    {
        $scriptUrl = str_replace(Simple::$app->getAppDir(), '', Simple::$app->getNcDir());
        $scriptUrl = trim($scriptUrl, DIRECTORY_SEPARATOR);
        $this->scriptUrl = $scriptUrl;
    }

    /**
     * Действие после выполнения
     */
    protected function afterHandle()
    {
    }

    /**
     * Установка обработчика ошибок
     */
    protected function initErrorHandler()
    {
        $handler = new ErrorHandler();
        $handler->logger = Simple::getLogger();
        $handler->debug = $this->getParam('debug', false);
        $handler->debugLog = $this->getParam('debugLog', false);
        set_error_handler([$handler, 'errorHandler']);
        set_exception_handler([$handler, 'exceptionHandler']);
    }

    /**
     * Конфигурация контейнера поумолчанию
     */
    protected function defaultConfigure()
    {

    }

    /**
     * Базовый конфиг
     * @return array
     */
    protected function getBaseConfig()
    {
        return [];
        return require $this->getNcDir() . '/config/config.php';
    }
}
