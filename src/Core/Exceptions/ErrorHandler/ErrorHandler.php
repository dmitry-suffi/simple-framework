<?php


namespace suffi\Simple\Core\Exceptions\ErrorHandler;

use suffi\Simple\Core\Logger;
use suffi\Simple\Core\nc;

/**
 * Обработчик ошибок
 *
 * Class ErrorHandler
 * @package suffi\Simple\Core\Exceptions\ErrorHandler
 */
class ErrorHandler
{

    /**
     * Логгер
     * @var Logger
     */
    public $logger = null;

    /**
     * Флаг дебага
     * @var bool
     */
    public $debug = false;

    /**
     * Флаг записи в лог
     * @var bool
     */
    public $writeLog = true;

    /**
     * Флаг расширенного логирования
     * @var bool
     */
    public $detailLog = false;

    /**
     * Метод перехвата ошибок
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     */
    public function errorHandler(int $errno, string $errstr, string $errfile = '', int $errline = 0, array $errcontext = [])
    {
        $this->handler(new \ErrorException($errstr, 0, $errno, $errfile, $errline));
    }

    /**
     * Метод перехвата исключений
     * @param \Throwable $ex
     */
    public function exceptionHandler(\Throwable $ex)
    {
        $this->handler($ex);
    }

    /**
     * Обработка исключений
     * @param \Throwable $ex
     */
    protected function handler(\Throwable $ex)
    {
        $errno = $ex->getCode();
        $errstr = $ex->getMessage();

        if ($this->debug) {
            $file = $ex->getFile();
            $line = $ex->getLine();

            $code = '';
            try {
                if (file_exists($file) && is_readable($file)) {
                    $strings = file($file);
                    $strings = array_slice($strings, max(0, $line - 10), 20);
                    if ($strings) {
                        $code = implode('', $strings);
                        if ($code) {
                            $code = '<?php ' . "\r\n" . $code;
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            echo $this->parseDebug($ex, $code);
        }

        if ($this->writeLog && $this->logger) {
            $fullMessage = $ex->getTraceAsString();

            if ($this->detailLog) {
                $fullMessage = $this->addGlobals($fullMessage);
            }

            if ($this->isError($errno)) {
                $this->logger->error($errstr, $fullMessage);
            } else {
                $this->logger->debug($errstr, $fullMessage);
            }
        }

        if ($this->isError($errno)) {
            $this->header500($errstr);
        }
    }

    /**
     * Определение по типу
     * @param int $errno
     * @return bool
     */
    protected function isError(int $errno):bool
    {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            default:
                return true;
                break;

            case E_NOTICE:
            case E_USER_ERROR:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return false;
                break;
        }
    }

    /**
     * Выбрасывает 500й статус и текст с ошибкой
     * @param string $errstr
     */
    protected function header500(string $errstr)
    {
        if (!$this->debug) {
            ob_clean();
        }

        if ($this->isAjax()) {
            echo json_encode(['error' => $errstr]);
        } else {
            if (!headers_sent()) {
                header('HTTP/1.1 500');
            }

            $staticPath = nc::$app->scriptUrl . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'static';

            echo '<html>
                        <body>
                        <div style="text-align: center">
                        <img src="' . $staticPath . '/css/images/503.gif" width="500px">
                        <h3>Ошибка!</h3>
                        <p>' . $errstr . '</p>
                        </div>
                        </body>
                    </html>';
        }
        exit;
    }

    /**
     * Парсинг шаблона
     * @param \Throwable $ex
     * @param string $code
     * @return mixed
     */
    protected function parseDebug(\Throwable $ex, $code = '')
    {
        if ($this->isAjax()) {
            return '';
        } else {
            ob_start();
            ob_implicit_flush(false);
            extract(['exception' => $ex, 'code' => $code], EXTR_OVERWRITE);
            require('errorTemplate.php');

            return ob_get_clean();
        }
    }

    /**
     * Добавление глобальных переменных в строку
     * @param $fullMessage
     * @return string
     */
    protected function addGlobals($fullMessage)
    {
        $fullMessage .= "\r\n";
        $fullMessage .= "\r\n";

        $fullMessage .= '$_GET';
        $fullMessage .= "\r\n";
        $fullMessage .= print_r($_GET, true);
        $fullMessage .= "\r\n";

        $fullMessage .= '$_POST';
        $fullMessage .= "\r\n";
        $fullMessage .= print_r($_POST, true);
        $fullMessage .= "\r\n";

        if (\PHP_SESSION_ACTIVE == session_status()) {
            $fullMessage .= '$_SESSION';
            $fullMessage .= "\r\n";
            $fullMessage .= print_r($_SESSION, true);
            $fullMessage .= "\r\n";
        }

        $fullMessage .= '$_COOKIE';
        $fullMessage .= "\r\n";
        $fullMessage .= print_r($_COOKIE, true);
        $fullMessage .= "\r\n";

        $fullMessage .= '$_SERVER';
        $fullMessage .= "\r\n";
        $fullMessage .= print_r($_SERVER, true);
        $fullMessage .= "\r\n";

        if (function_exists('pinba_get_info')) {
            $fullMessage .= 'Pinba';
            $fullMessage .= "\r\n";
            $fullMessage .= print_r(pinba_get_info(), true);
            $fullMessage .= "\r\n";
        }

        return $fullMessage;
    }

    /**
     * Проверка на ajax запрос
     * @return bool
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
