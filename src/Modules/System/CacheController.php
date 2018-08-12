<?php

namespace suffi\Simple\Modules\System;

use suffi\Simple\Core\Simple;

/**
 * Контроллер для работы с кэшем
 *
 * Class CacheController
 * @package suffi\Simple\Modules\System
 *
 * <pre>
 *         'modules' => [
 *              'System' => [
 *                  'class' => 'suffi\Simple\Modules\System\Module',
 *              ],
 * </pre>
 */
class CacheController extends \suffi\Simple\Core\Controller
{
    /**
     * Действие по умолчанию
     * @var string
     */
    protected $defaultAction = 'clear';

    /**
     * Очистка кеша
     *
     * index.php?route=System/Cache/clear
     */
    public function actionClear()
    {
        $cache = Simple::get('Cache');
        if ($cache instanceof Cache) {
            if ($cache->clear()) {
                echo "Данные кеша очищены!";
                return;
            }
        }

        echo "Данные кеша не очищены!";
    }
}
