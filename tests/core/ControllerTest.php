<?php

namespace suffi\Simple\tests\core;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Core\Controller;
use suffi\Simple\Core\Exceptions\NotFoundException;

/**
 * Class RouterTest
 */
class ControllerTest extends TestCase
{


    /**
     * @return Controller
     */
    protected function getController()
    {
        return new class extends Controller {

            public $foo = '';

            public $defaultAction = 'bar';

            public function beforeAction($actionName) {
                $this->foo .= 'before';
            }

            public function afterAction($actionName) {
                $this->foo .=  'after';
            }

            public function actionTest() {
                $this->foo .= 'test';
                return 'test';
            }

            public function actionBar() {
                return 'bar';
            }

            public function actionFooBar() {
                return 'foo-bar';
            }
        };
    }

    public function testAction()
    {
        $c = $this->getController();

        $this->assertEquals($c->foo, '');
        $this->assertEquals($c->run('test'), 'test');
        $this->assertEquals($c->foo, 'beforetestafter');

        $this->assertEquals($c->run('bar'), 'bar');
        $this->assertEquals($c->run('noexist'), 'bar');
        $this->assertEquals($c->run('foo-bar'), 'foo-bar');
    }

    public function testActionNotExist()
    {
        $this->expectException(NotFoundException::class);
        $c =  new class extends Controller {

            public $defaultAction = 'bar';
        };

        /** @noinspection PhpWrongStringConcatenationInspection */
        $c->defaultAction = 'noexist';

        $this->assertEquals($c->run('noexist'), 'bar');
    }

}
