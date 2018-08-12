<?php

namespace suffi\Simple\Components\Smarty;

use suffi\Simple\Core\Simple;

/**
 * Представление через шаблонизатор Smarty
 * Class View
 * @package suffi\Simple\Components\Smarty
 */
class View extends \suffi\Simple\Core\View
{
    /**
     * Объект Smarty
     * @var \Smarty
     */
    private $smarty = null;

    /**
     * Папка шаблонов
     * @var string
     */
    public $templateDir = 'Views';

    /**
     * Папка для скомпиленных шаблонов
     * @var string
     */
    public $compileDir = 'templates_c';

    /**
     * {@inheritdoc}
     */
    public function getTemplateDir($index)
    {
        return $this->getSmarty()->getTemplateDir($index);
    }

    /**
     * Объект Smatry
     * @return \Smarty
     */
    public function getSmarty()
    {
        if (is_null($this->smarty)) {
            $this->smarty = new \Smarty();

            $this->smarty->setCompileDir(Simple::$app->getAppDir() . DIRECTORY_SEPARATOR . $this->compileDir);

            $this->smarty->assign(
                'staticPath',
                Simple::$app->scriptUrl . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'static'
            );

            /** Вырубаем непреднамеренные ошибки */
            \Smarty::muteExpectedErrors();
        }

        return $this->smarty;
    }

    /**
     * {@inheritdoc}
     */
    public function addTemplateDir($addTemplateDir)
    {
        $this->getSmarty()->addTemplateDir($addTemplateDir);
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, $data = [])
    {

        $smarty = $this->getSmarty();

        foreach ($data as $name => $value) {
            $smarty->assign($name, $value);
        }

        $smarty->display($template . '.tpl');
    }

    /**
     * Очистка временных данных
     */
    public function clear()
    {
        $smarty = $this->getSmarty();
        $smarty->clearCompiledTemplate();
    }
}
