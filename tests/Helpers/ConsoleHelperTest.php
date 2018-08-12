<?php

namespace suffi\Simple\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Helpers\ConsoleHelper;

class ConsoleHelperTest extends TestCase
{

    public function testSet()
    {
        $colorString = ConsoleHelper::color('text', '31');

        $this->assertEquals($colorString, "\033[31m text \033[0m");
    }

}