<?php

namespace suffi\Simple\Tests\Components\View;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Components\View\PhpView;

class PhpViewTest extends TestCase
{

    public function testRender()
    {
        $view = new PhpView();
        $view->templateDir = __DIR__ . DIRECTORY_SEPARATOR;

        ob_start();
        ob_implicit_flush(false);
        $view->render("view", [
            "name" => "Kristian",
            "age" => 30
        ]);
        $r = ob_get_clean();

        $this->assertEquals($r, "Привет, я Kristian, мне 30 лет.");
    }
}
