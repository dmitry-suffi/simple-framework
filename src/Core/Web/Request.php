<?php

namespace suffi\Simple\Core\Web;

/**
 * Класс для доступа к данным http-запроса
 * Class Request
 * @package suffi\Simple\Core
 */
class Request extends \suffi\Simple\Core\Request
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;
    }

}
