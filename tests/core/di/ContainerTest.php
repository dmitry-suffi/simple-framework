<?php

namespace suffi\Simple\tests\core\di;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Core\di\Container;
use \suffi\Simple\tests\core\di\fixtures;

class ContainerTest extends TestCase
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

    public function testDefinition()
    {
        $container = $this->getContainer();

        $container->setDefinition('common', self::classNameCommon)
            ->parameter('foo', 'foo')
            ->property('bar', 'bar')
            ->setter('thy', 'thy');

        $this->assertTrue($container->hasDefinition('common'));

        $def = $container->getDefinition('common');
        $this->assertInstanceOf('suffi\Simple\Core\di\Definition', $def);

        $container->removeDefinition('common');

        $this->assertFalse($container->hasDefinition('common'));
        $this->assertFalse($container->getDefinition('common'));
    }

    public function testContainer()
    {
        $container = $this->getContainer();

        $foo = new fixtures\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

        $container->set('foo', $foo);

        $this->assertTrue($container->has('foo'));

        $newFoo = $container->get('foo');

        $this->assertInstanceOf(self::classNameFOO, $newFoo);
        $this->assertEquals($newFoo->foo, 'foo');
        $this->assertEquals($newFoo, $foo);

        $foo1 = new fixtures\Foo();
        $foo1->foo = 'bar';

        $container->set('foo', $foo1);

        $newFoo1 = $container->get('foo');

        $this->assertInstanceOf(self::classNameFOO, $newFoo1);
        $this->assertEquals($newFoo1->foo, 'bar');
        $this->assertEquals($newFoo1, $foo1);
        $this->assertNotEquals($newFoo1, $foo);

        $this->assertFalse($container->get('bar'));

        $this->assertTrue($container->has('foo'));
        $container->remove('foo');

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

    }

    public function testSingleton()
    {
        $container = $this->getContainer();

        $foo = new fixtures\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf(self::classNameFOO, $newFoo);
        $this->assertEquals($foo, $newFoo);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testErrorSingleton()
    {
        $container = $this->getContainer();

        $foo = new fixtures\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf(self::classNameFOO, $newFoo);
        $this->assertEquals($foo, $newFoo);

        $this->initException();
        $container->setSingleton('foo', $foo);
    }

    public function testGet()
    {
        $container = $this->getContainer();

        $foo = new fixtures\Foo();
        $foo->foo = 'foo';

        $bar = new fixtures\Bar('foo', 'bar');

        $thy = new fixtures\Thy();

        $thy->setFoo('foo');
        $thy->setBar('bar');

        $container->set('foo', $foo);
        $container->set('bar', $bar);
        $container->set('thy', $thy);

        $container->setDefinition('common', self::classNameCommon)
            ->parameter('foo', 'foo')
            ->property('bar', $bar)
            ->setter('thy', 'thy');

        $this->assertFalse($container->has('common'));

        /** @var fixtures\Common $common */
        $common = $container->get('common');

        $this->assertInstanceOf(self::classNameCommon, $common);

        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, $bar);
        $this->assertEquals($common->getThy(), $thy);
    }

    public function testGetRetry()
    {
        $container = $this->getContainer();

        $container->setDefinition('foo', self::classNameFOO);

        $container->setDefinition('common', self::classNameCommon)
            ->parameter('foo', 'foo');

        $this->assertFalse($container->has('common'));
        $this->assertFalse($container->has('foo'));

        /** @var fixtures\Common $common */
        $common = $container->get('common');

        $this->assertTrue($container->has('foo'));

        $foo = $container->get('foo');
        /** @var fixtures\Foo $foo */
        $foo->bar = 'foo';

        $this->assertEquals($common->getFoo()->bar, $foo->bar);

    }

}
