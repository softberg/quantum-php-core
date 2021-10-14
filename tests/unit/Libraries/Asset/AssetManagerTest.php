<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Asset\Asset;
use Quantum\Di\Di;
use Quantum\App;

/**
 * @runTestsInSeparateProcesses
 */
class AssetManagerTest extends TestCase
{

    private $assetManager;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

        Di::loadDefinitions();

        config()->set('base_url', 'http://mydomain.com');

        $this->assetManager = AssetManager::getInstance();
    }

    public function testRegisterPublishDump()
    {
        $this->assetManager->register([
            new Asset(Asset::CSS, 'css/style.css'),
            new Asset(Asset::CSS, 'css/responsive.css')
        ]);

        $this->assetManager->register([
            new Asset(Asset::JS, 'js/bootstrap.js', 1),
            new Asset(Asset::JS, 'js/bootstrap-datepicker.min.js', 2)
        ]);

        $this->assetManager->register([
            new Asset(Asset::CSS, 'css/reset.css', 0),
            new Asset(Asset::CSS, 'css/media.css', 2)
        ]);

        $this->assetManager->register([
            new Asset(Asset::JS, 'js/jquery.js'),
            new Asset(Asset::JS, 'js/custom.js')
        ]);

        $expectedOutput = '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/reset.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/style.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/media.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/responsive.css">' . PHP_EOL;

        ob_start();

        $this->assetManager->dump(AssetManager::CSS_STORE);

        $this->assertStringContainsString($expectedOutput, ob_get_contents());

        ob_clean();

        $expectedOutput = '<script src="http://mydomain.com/assets/js/jquery.js"></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/bootstrap.js"></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/bootstrap-datepicker.min.js"></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/custom.js"></script>' . PHP_EOL;

        $this->assetManager->dump(AssetManager::JS_STORE);

        $this->assertStringContainsString($expectedOutput, ob_get_contents());

        ob_get_clean();
    }

    public function testAssetUrl()
    {
        $this->assertEquals('http://mydomain.com/assets/icons/person.png', $this->assetManager->url('icons/person.png'));

        $this->assertEquals('http://mydomain.com/assets/fonts/arial.ttf', $this->assetManager->url('fonts/arial.ttf'));
    }

}
