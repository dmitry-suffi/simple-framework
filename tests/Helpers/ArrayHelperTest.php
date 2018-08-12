<?php

namespace suffi\Simple\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Helpers\ArrayHelper;

class ArrayHelperTest extends TestCase
{

    public function testGet()
    {
        $array = [
            'foo' => 'foo',
            'bar' => [
                'foo' => 'barfoo',
                'bar' => 'bar',
                'barfoo' => [
                    'bar' => 'barfoofoo'
                ]
            ]
        ];

        $this->assertEquals(ArrayHelper::get([], 'foo'), null);
        $this->assertEquals(ArrayHelper::get($array, []), null);
        $this->assertEquals(ArrayHelper::get($array, false), null);
        $this->assertEquals(ArrayHelper::get($array, 'foo'), 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'foo', 'bar'), 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'foobar'), null);
        $this->assertEquals(ArrayHelper::get($array, 'foobar', 'bar'), 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'foobar', 'foo'), 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), 'barfoo');
        $this->assertEquals(ArrayHelper::get($array, 'bar.bar'), 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'bar.bar', 'foo'), 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'bar.barfoo'), ['bar' => 'barfoofoo']);
        $this->assertEquals(ArrayHelper::get($array, 'bar.barfoo.bar'), 'barfoofoo');
        $this->assertEquals(ArrayHelper::get($array, 'bar.barfoo.foo'), null);
    }

    public function testSet()
    {
        $array = [
            'foo' => 'foo',
            'bar' => [
                'foo' => 'barfoo',
                'bar' => 'bar',
                'barfoo' => [
                    'bar' => 'barfoofoo'
                ]
            ]
        ];
        $this->assertEquals(ArrayHelper::get($array, 'foo'), 'foo');

        ArrayHelper::set($array, 'foo', 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'foo'), 'bar');

        ArrayHelper::set($array, 'foo', 'barfoo');
        $this->assertEquals(ArrayHelper::get($array, 'foo'), 'barfoo');

        $this->assertEquals(ArrayHelper::get($array, 'barfoo'), null);
        ArrayHelper::set($array, 'barfoo', 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'barfoo'), 'bar');

        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), 'barfoo');
        ArrayHelper::set($array, 'bar.foo', 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), 'bar');

        $this->assertEquals(ArrayHelper::get($array, 'foobar.foo'), null);
        ArrayHelper::set($array, 'foobar.foo', 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'foobar.foo'), 'bar');

        $this->assertEquals(ArrayHelper::get($array, 'foobar.foo.bar'), null);
        ArrayHelper::set($array, 'foobar.foo.bar', 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'foobar.foo.bar'), null);
        $this->assertEquals(ArrayHelper::get($array, 'foobar.foo'), 'bar');

        ArrayHelper::set($array, 'aaa.foo.bar', 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'aaa.foo.bar'), 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'aaa.foo'), ['bar' => 'foo']);
        $this->assertEquals(ArrayHelper::get($array, 'aaa'), ['foo' => ['bar' => 'foo']]);
    }

    public function testHas()
    {

        $array = [
            'foo' => 'foo',
            'bar' => [
                'foo' => 'barfoo',
                'bar' => 'bar',
                'barfoo' => [
                    'bar' => 'barfoofoo'
                ]
            ]
        ];

        $this->assertFalse(ArrayHelper::has([], 'foo'));
        $this->assertFalse(ArrayHelper::has($array, []));
        $this->assertFalse(ArrayHelper::has($array, false));
        $this->assertTrue(ArrayHelper::has($array, 'foo'));
        $this->assertFalse(ArrayHelper::has($array, 'foobar'));
        $this->assertTrue(ArrayHelper::has($array, 'bar.foo'));
        $this->assertTrue(ArrayHelper::has($array, 'bar.bar'));
        $this->assertTrue(ArrayHelper::has($array, 'bar.barfoo'));
        $this->assertFalse(ArrayHelper::has($array, 'bar.barfoofoo'));
        $this->assertTrue(ArrayHelper::has($array, 'bar.barfoo.bar'));
        $this->assertFalse(ArrayHelper::has($array, 'bar.barfoo.foo'));
    }

    public function testRemove()
    {

        $array = [
            'foo' => 'foo',
            'bar' => [
                'foo' => 'barfoo',
                'bar' => 'bar',
                'barfoo' => [
                    'bar' => 'barfoofoo'
                ]
            ]
        ];

        $this->assertEquals(ArrayHelper::get($array, 'foo'), 'foo');
        ArrayHelper::remove($array, 'foo');
        $this->assertEquals(ArrayHelper::get($array, 'foo'), null);

        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), 'barfoo');
        $this->assertEquals(ArrayHelper::get($array, 'bar.bar'), 'bar');
        ArrayHelper::remove($array, 'bar.foo');
        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), null);
        $this->assertEquals(ArrayHelper::get($array, 'bar.bar'), 'bar');

        $this->assertEquals(ArrayHelper::get($array, 'bar.barfoo.bar'), 'barfoofoo');
        ArrayHelper::remove($array, 'bar.barfoo.bar');
        $this->assertEquals(ArrayHelper::get($array, 'bar.barfoo.bar'), null);

        $this->assertEquals(ArrayHelper::get($array, 'bar.foo'), null);
        $this->assertEquals(ArrayHelper::get($array, 'bar.bar'), 'bar');
        $this->assertEquals(ArrayHelper::get($array, 'foo'), null);
    }

    public function testIndex()
    {

        $array = [
            ['id' => '1', 'name' => 'vasya'],
            ['id' => '4', 'name' => 'olya'],
            ['id' => '7', 'name' => 'sasha'],
        ];

        $idArray = ArrayHelper::index($array, 'id');

        $this->assertEquals(count($idArray), 3);
        $this->assertEquals($idArray['4'], ['id' => '4', 'name' => 'olya']);
        $this->assertEquals(array_diff_key(array_keys($idArray), ['1', '4', '7']), []);

        $idArray = ArrayHelper::index($array, 'name');

        $this->assertEquals(count($idArray), 3);
        $this->assertEquals($idArray['olya'], ['id' => '4', 'name' => 'olya']);
        $this->assertEquals(array_diff_key(array_keys($idArray), ['olya', 'vasya', 'sasha']), []);
    }

    public function testGroups()
    {
        $array = [
            ['id' => '1', 'name' => 'vasya', 'city' => 'astana'],
            ['id' => '4', 'name' => 'olya', 'city' => 'smolensk'],
            ['id' => '7', 'name' => 'sasha', 'city' => 'smolensk'],
        ];

        $grArray = ArrayHelper::groups($array, 'city');

        $this->assertEquals(count($grArray), 2);
        $this->assertEquals(count($grArray['smolensk']), 2);
        $this->assertEquals(count($grArray['astana']), 1);
        $this->assertEquals($grArray['astana'], [['id' => '1', 'name' => 'vasya', 'city' => 'astana']]);
        $this->assertEquals(array_diff_key(array_keys($grArray), ['astana', 'smolensk', '7']), []);
    }

    public function testMerge()
    {
        $array = [
            'foo' => 'foo',
            'barfoo' => 'barfoo',
            'bar' => [
                'foo' => 'barfoo',
                'bar' => 'bar',
                'barfoo' => [
                    'bar' => 'barfoofoo'
                ]
            ]
        ];

        $array2 = [
            'foo' => 'bar',
            'foobar' => 'foobar',
            'bar' => [
                'bar' => 'foo',
                'foobar' => 'foo',
                'barfoo' => [
                    'bar' => ['foo' => 'foo'],
                    'foo' => 'foo'
                ]
            ]
        ];

        $mergeArray = ArrayHelper::merge($array, $array2);

        $this->assertEquals(count($mergeArray), 4);
        $this->assertEquals($mergeArray['foo'], 'bar');
        $this->assertEquals($mergeArray['barfoo'], 'barfoo');
        $this->assertEquals($mergeArray['foobar'], 'foobar');
        $this->assertEquals($mergeArray['bar']['bar'], 'foo');
        $this->assertEquals($mergeArray['bar']['foo'], 'barfoo');
        $this->assertEquals($mergeArray['bar']['foobar'], 'foo');
        $this->assertEquals($mergeArray['bar']['barfoo']['bar'], ['foo' => 'foo']);
        $this->assertEquals($mergeArray['bar']['barfoo']['foo'], 'foo');
    }
}
