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

    const textColorRed = 31;
    const textColorGren = 32;
    const textColorYellow = 33;

    public static function color(string $text, int $color) {
        return "\033[" . $color . "m " . $text . " \033[0m";
    }

}