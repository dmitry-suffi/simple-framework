<?php

namespace suffi\Simple\Components\View;

use suffi\Simple\Core\View;

class PhpView extends View
{
    public function render($template, $data = [])
    {
        echo $this->renderFile($template, $data);
    }

    /**
     * @param $template
     * @param $data
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    protected function renderFile($template, $data): string
    {
        ob_start();
        ob_implicit_flush(false);
        extract($data, EXTR_OVERWRITE);
        try {
            require $this->templateDir . $template . '.php';
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_clean();
            throw $e;
        } catch (\Throwable $e) {
            ob_clean();
            throw $e;
        }
    }
}
