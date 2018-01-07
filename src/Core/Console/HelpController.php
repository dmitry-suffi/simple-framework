<?php

namespace suffi\Simple\Core\Console;


use suffi\Simple\Core\Controller;
use suffi\Simple\Core\Module;
use suffi\Simple\Core\nc;
use suffi\Simple\Helpers\ConsoleHelper;

/**
 * Class HelpController
 * @package suffi\Simple\Core\Console
 */
class HelpController extends Controller
{
    /**
     * Действие по умолчанию
     * @var string
     */
    protected $defaultAction = 'help';


    public function actionHelp()
    {
        echo PHP_EOL;
        echo "Список консольных команд:" . PHP_EOL;

        $modules = nc::$app->getParam('modules');

        foreach ($modules as $module) {
            if (!isset($module['class'])) {
                continue;
            }
            $instance = new $module['class'];
            if (!($instance instanceof Module)) {
                continue;
            }
            if ($instance->consoleCommand) {
                echo PHP_EOL;
                echo $instance->getTitle() . "\n";
                echo PHP_EOL;

                foreach ($instance->consoleCommand as $name => $title) {
                    echo ConsoleHelper::color($name, ConsoleHelper::textColorGren) . " - " . $title . "\n";
                }
            }
        }

    }

}