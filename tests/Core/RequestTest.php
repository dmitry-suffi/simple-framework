<?php

namespace suffi\Simple\Tests\Core;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Core\Request;


/**
 * Class RequestTest
 */
class RequestTest extends TestCase
{

    protected function getRequest(array $get = [], array $post = [])
    {
        $request =  new class extends Request
        {
            public $get;
            public $post;
            public function init()
            {
                $this->queryParams = $this->get;
                $this->bodyParams = $this->post;
            }
        };

        $request->get = $get;
        $request->post = $post;

        $request->init();

        return $request;
    }

    public function testRequest()
    {
        $get = [
            'a' => 'ga' . rand(0, 10),
            'b' => 'gb' . rand(0, 10),
            'c' => 'gc' . rand(0, 10)
        ];

        $post = [
            'a' => 'pa' . rand(0, 10),
            'b' => 'pb' . rand(0, 10),
            'c' => 'pc' . rand(0, 10)
        ];


        $request = $this->getRequest($get, $post);

        $this->assertEquals($request->get('a'), $get['a']);
        $this->assertEquals($request->get('b'), $get['b']);
        $this->assertEquals($request->get('c'), $get['c']);
        $this->assertNull($request->get('d'));
        $this->assertEquals($request->get('d', 'ddd'), 'ddd');
        $this->assertEquals($request->get('a', 'ddd'), $get['a']);

        $query = $request->getQueryParams();

        $this->assertArraySubset($query, $get);
        $this->assertArraySubset($get, $query);

        $this->assertEquals($request->post('a'), $post['a']);
        $this->assertEquals($request->post('b'), $post['b']);
        $this->assertEquals($request->post('c'), $post['c']);
        $this->assertNull($request->post('d'));
        $this->assertEquals($request->post('d', 'ddd'), 'ddd');
        $this->assertEquals($request->post('a', 'ddd'), $post['a']);

        $query = $request->getBodyParams();

        $this->assertArraySubset($query, $post);
        $this->assertArraySubset($post, $query);
    }
}
