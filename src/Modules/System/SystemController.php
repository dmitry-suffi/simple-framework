<?php

namespace suffi\Simple\Modules\System;

use suffi\Simple\Core\Simple;
use suffi\Simple\Core\View;

/**
 * Контроллер для системных действий
 *
 * Class SystemController
 * @package suffi\Simple\Modules\System
 *
 * <pre>
 *         'modules' => [
 *              'System' => [
 *                  'class' => 'suffi\Simple\Modules\System\Module',
 *              ],
 * </pre>
 */
class SystemController extends \suffi\Simple\Core\Controller
{
    /**
     * Действие по умолчанию
     * @var string
     */
    protected $defaultAction = 'clear';

    /**
     * Очищение данных представления.
     * Очищает временные файлы представления на текущем сервере.
     *
     * index.php?route=System/System/clear
     */
    public function actionClear()
    {
        /** @var View $view */
        $view = Simple::get('View');
        $view->clear();

        echo "Данные очищены!";
    }
}
