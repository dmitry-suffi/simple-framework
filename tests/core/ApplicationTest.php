<?php

namespace suffi\Simple\tests\core;

use PHPUnit\Framework\TestCase;
use suffi\Simple\Core\Exceptions\ConfigException;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends TestCase
{

    /**
     * @return \suffi\Simple\Core\Application
     */
    protected function getApp()
    {
        $app = new \suffi\Simple\Core\Application();
        $app->useErrorHandler = false;
        return $app;
    }

    public function testConfig()
    {
        $app = $this->getApp();

        $app->init(['params' => [
            'param1' => '1',
            'param2' => '2',
            'modules' => [
                'Call' => [
                    'class' => 'suffi\Simple\Modules\Call\Module'
                ]
            ],
        ]]);

        $this->assertEquals($app->getParam('param1'), 1);
        $this->assertEquals($app->getParam('param2'), 2);
        $this->assertNull($app->getParam('param3'));
        $this->assertEquals($app->getParam('param3', 3), 3);
    }

    public function testPath()
    {
        $app = $this->getApp();

        $app->setAppDir('/test/test2/test3/');
        $this->assertEquals($app->getAppDir(), '/test/test2/test3/');
    }

    public function testBuildVersion()
    {
        $app = $this->getApp();

        $this->assertNull($app->getBuildVersion());

        $app = $this->getApp();

        defined('BUILD_VERSION') || define('BUILD_VERSION', '1');
        $this->assertEquals($app->getBuildVersion(), '1');

        $app = $this->getApp();

        $app->init(['params' => [
            'build_version' => '2',
        ]]);

        $this->assertEquals($app->getBuildVersion(), '2');
    }

    public function testBadRun()
    {
        $this->expectException(ConfigException::class);

        $app = $this->getApp();

        $app->getContainer()->removeDefinition('router');

        $app->run();
    }

}
