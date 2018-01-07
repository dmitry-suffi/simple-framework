<?php

namespace suffi\Simple\Core\Console;

/**
 * Класс для доступа к данным запроса для консоли
 * Class Request
 * @package suffi\Simple\Core
 */
class Request extends \suffi\Simple\Core\Request
{
    public function init()
    {
        $this->queryParams = array_slice($_SERVER['argv'], 1);
        $this->bodyParams = [];
    }

}
