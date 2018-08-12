<?php

namespace suffi\Simple\Helpers;

/**
 * Class ConsoleHelper
 *
 * Хелпер для работы с консолью
 *
 * @package suffi\Simple\Helpers
 */
class ConsoleHelper
{

    const TEXT_COLOR_RED = 31;
    const TEXT_COLOR_GREEN = 32;
    const TEXT_COLOR_YELLOW = 33;

    public static function color(string $text, int $color)
    {
        return "\033[" . $color . "m " . $text . " \033[0m";
    }
}
