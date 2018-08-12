<?php

namespace suffi\Simple\Tests\core;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Core\Exceptions\ConfigException;
use suffi\Simple\Core\Module;

/**
 * Class ModuleTest
 */
class ModuleTest extends TestCase
{

    public function testConfig()
    {
        $module = new Module();

        $module->params = [
            'param1' => '1',
            'param2' => '2'
        ];

        $this->assertEquals($module->getParam('param1'), 1);
        $this->assertEquals($module->getParam('param2'), 2);
        $this->assertNull($module->getParam('param3'));
        $this->assertEquals($module->getParam('param3', 3), 3);
    }

    /**
     * @return Module
     */
    protected function getModule()
    {
        return new class extends Module
        {
            protected $name = 'Foo';

            protected $controllerMap = [
                'Foo' => 'suffi\Simple\Tests\Fixtures\core\FooController',
                'Bad' => '\StdClass',
            ];
        };
    }

    public function testController()
    {
        $module = $this->getModule();

        $this->assertEquals($module->getName(), 'Foo');

        $this->assertTrue($module->hasController('Foo'));
        $this->assertFalse($module->hasController('Bar'));

        $controller = $module->getController('Foo');

        $this->assertInstanceOf('suffi\Simple\Tests\Fixtures\core\FooController', $controller);
        $this->assertEquals($controller->getModule(), $module);

        $this->assertEquals($module->getName(), 'Foo');

        $module->addJs('test', 'app');
        $module->addCss('testcss', 'app');

        $this->assertEquals($module->config['js'], ['app' => ['test']]);
        $this->assertEquals($module->config['css'], ['app' => ['testcss']]);

    }

    public function testNotExistController()
    {

        $this->expectException(ConfigException::class);

        $module = $this->getModule();

        $this->assertTrue($module->hasController('Bad'));

        $module->getController('Bad');
    }

}
