<?php

namespace suffi\Simple\tests\core\di;

use PHPUnit\Framework\TestCase;
use \suffi\Simple\Core\di\Container;
use \suffi\Simple\Core\di\Definition;
use \suffi\Simple\tests\core\di\fixtures;

/**
 * Class DefinitionTest
 */
class DefinitionTest extends TestCase
{
    const classNameFOO = 'suffi\Simple\tests\core\di\fixtures\Foo';
    const classNameBar = 'suffi\Simple\tests\core\di\fixtures\Bar';
    const classNameCommon = 'suffi\Simple\tests\core\di\fixtures\Common';
    const classNameInit = 'suffi\Simple\tests\core\di\fixtures\Init';
    const classNameThy = 'suffi\Simple\tests\core\di\fixtures\Thy';

    /**
     * @return Container
     */
    protected function getContainer():Container
    {
        $container = new Container();
        return $container;
    }

    protected function initException()
    {
        $this->expectException(\suffi\Simple\Core\di\Exception::class);
    }

    public function testProperties()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'foo', self::classNameFOO);

        $def->property('foo', 'foo')
            ->property('bar', 'bar');

        $foo = $def->make();

        $this->assertInstanceOf(self::classNameFOO, $foo);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $def->property('foo', 'foo1')
            ->property('bar', 'bar1');

        $foo1 = $def->make();

        $this->assertInstanceOf(self::classNameFOO, $foo);
        $this->assertInstanceOf(self::classNameFOO, $foo1);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $def2 = new Definition($container, 'foo', self::classNameFOO);
        $def2->property('foo', 'foo2');

        $foo2 = $def2->make();

        $this->assertInstanceOf(self::classNameFOO, $foo);
        $this->assertInstanceOf(self::classNameFOO, $foo1);
        $this->assertInstanceOf(self::classNameFOO, $foo2);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $this->assertEquals($foo2->foo, 'foo2');
        $this->assertEquals($foo2->bar, '');

        /** Static */
        $def->property('s_foo', 'foo');

        $this->assertNotEquals(fixtures\Foo::$s_foo, 'foo');
        $foo3 = $def->make();

        $this->assertInstanceOf(self::classNameFOO, $foo3);

        $this->assertEquals(fixtures\Foo::$s_foo, 'foo');

        /** Callable */

        $def = new Definition($container, 'foo', self::classNameFOO);

        $def->property('foo', function() {
            return 'foo';
        })
            ->property('bar', function() {
                return 'foo';
            });

        $foo = $def->make();
        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'foo');

    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testPrivateProperty()
    {

        $container = $this->getContainer();

        $def = new Definition($container, 'foo', self::classNameFOO);

        $def->property('_foo', 'foo');

        $this->initException();
        $def->make();
    }

    public function testConstructor()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', self::classNameBar);

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        /** @var fixtures\Bar $bar */
        $bar = $def->make();

        $this->assertInstanceOf(self::classNameBar, $bar);
        $this->assertEquals($bar->getFoo(), 'foo');
        $this->assertEquals($bar->getBar(), 'bar');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $def->parameter('foo', 'foo1')
            ->parameter('bar', 'bar1')
            ->parameter('thy', 'thy1');

        /** @var fixtures\Bar $bar */
        $bar1 = $def->make();

        $this->assertInstanceOf(self::classNameBar, $bar1);
        $this->assertEquals($bar1->getFoo(), 'foo1');
        $this->assertEquals($bar1->getBar(), 'bar1');
        $this->assertEquals($bar1->getThy(), 'thy1');

        /** Callable */
        $def->parameter('foo', function() {
            return 'foo1bar1';
        })
            ->parameter('bar', 'bar1')
            ->parameter('thy', 'thy1');

        /** @var fixtures\Bar $bar */
        $bar1 = $def->make();

        $this->assertInstanceOf(self::classNameBar, $bar1);
        $this->assertEquals($bar1->getFoo(), 'foo1bar1');
        $this->assertEquals($bar1->getBar(), 'bar1');
        $this->assertEquals($bar1->getThy(), 'thy1');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testNoSetParameters()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', self::classNameBar);

        $def->parameter('foo', 'foo');

        $this->initException();
        $bar = $def->make();
    }

    public function testSetters()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'thy', self::classNameThy);

        /** @var fixtures\Thy $thy */
        $thy = $def->make();

        $this->assertInstanceOf(self::classNameThy, $thy);
        $this->assertEquals($thy->getFoo(), '');
        $this->assertEquals($thy->getBar(), '');
        $this->assertEquals(fixtures\Thy::getSFoo(), '');

        $def->setter('foo', 'foo')
            ->setter('bar', 'bar');

        /** @var fixtures\Thy $thy1 */
        $thy1 = $def->make();

        $this->assertInstanceOf(self::classNameThy, $thy1);
        $this->assertEquals($thy1->getFoo(), 'foo');
        $this->assertEquals($thy1->getBar(), 'bar');
        $this->assertEquals(fixtures\Thy::getSFoo(), '');

        $def->setter('s_foo', 's_foo');

        $def->make();
        $this->assertEquals(fixtures\Thy::getSFoo(), 's_foo');

        /** @var fixtures\Thy $thy1 */
        $def->setter('foo', 'foo')
            ->setter('bar', function() {
                return 'foo1bar1';
            });

        $thy2 = $def->make();

        $this->assertInstanceOf(self::classNameThy, $thy2);
        $this->assertEquals($thy2->getFoo(), 'foo');
        $this->assertEquals($thy2->getBar(), 'foo1bar1');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testPrivateSetter()
    {

        $container = $this->getContainer();

        $def = new Definition($container, 'thy', self::classNameThy);

        $def->setter('foo-bar', 'foo');

        $this->initException();
        $def->make();
    }

    public function testInit()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'init', self::classNameInit);

        /** @var fixtures\Init $init */
        $init = $def->make();

        $this->assertInstanceOf(self::classNameInit, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, '');

        $def->init('initFoo');

        /** @var fixtures\Init $init */
        $init = $def->make();

        $this->assertInstanceOf(self::classNameInit, $init);
        $this->assertEquals($init->foo, 'foo');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, '');

        $def->init('initBar');

        /** @var fixtures\Init $init */
        $init = $def->make();

        $this->assertInstanceOf(self::classNameInit, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, 'bar');
        $this->assertEquals($init->thy, '');

        $def->init('initThy');

        /** @var fixtures\Init $init */
        $init = $def->make();

        $this->assertInstanceOf(self::classNameInit, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, 'thy');

        $def->init('initAll');

        /** @var fixtures\Init $init */
        $init = $def->make();

        $this->assertInstanceOf(self::classNameInit, $init);
        $this->assertEquals($init->foo, 'foo');
        $this->assertEquals($init->bar, 'bar');
        $this->assertEquals($init->thy, 'thy');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testNoExistInit()
    {

        $container = $this->getContainer();

        $def = new Definition($container, 'init', self::classNameInit);

        $def->init('initNoExist');

        $this->initException();
        $def->make();
    }

    public function testCommon()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'common', self::classNameCommon);

        $foo = new fixtures\Foo();
        $thy = new fixtures\Thy();

        /** @var fixtures\Common $common */
        $common = $def->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), '');
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def->parameter('foo', $foo);

        $common = $def->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def->property('bar', 'bar');

        $common = $def->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), '');

        $def->setter('thy', $thy);

        $common = $def->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), $thy);

        $def->init('initAll');

        $common = $def->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), 'foo init');
        $this->assertEquals($common->bar, 'bar init');
        $this->assertEquals($common->getThy(), 'thy init');

        /** All */
        $def1 = new Definition($container, 'common', self::classNameCommon);
        $common = $def1->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), '');
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def1->parameter('foo', $foo)
            ->property('bar', 'bar')
            ->setter('thy', $thy);

        $common = $def1->make();

        $this->assertInstanceOf(self::classNameCommon, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), $thy);
    }

}
