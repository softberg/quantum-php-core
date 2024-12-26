<?php

namespace Quantum\Tests\Libraries\Asset;

use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Asset\Asset;
use Quantum\Tests\AppTestCase;

/**
 * @runTestsInSeparateProcesses
 */
class AssetManagerTest extends AppTestCase
{

    private $assetManager;

    public function setUp(): void
    {
        parent::setUp();

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
            new Asset(Asset::JS, 'js/bootstrap.js', '', 1),
            new Asset(Asset::JS, 'js/bootstrap-datepicker.min.js', '', 2)
        ]);

        $this->assetManager->register([
            new Asset(Asset::CSS, 'css/reset.css', '', 0),
            new Asset(Asset::CSS, 'css/media.css', '', 2)
        ]);

        $this->assetManager->register([
            new Asset(Asset::JS, 'js/jquery.js'),
            new Asset(Asset::JS, 'js/custom.js', '', -1, ['async', 'defer']),
        ]);

        $this->assetManager->registerAsset(new Asset(Asset::JS, 'https://code.jquery.com/jquery-3.2.1.min.js'));

        $expectedOutput = '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/reset.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/style.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/media.css">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="http://mydomain.com/assets/css/responsive.css">' . PHP_EOL;

        ob_start();

        $this->assetManager->dump(AssetManager::STORES['css']);

        $this->assertStringContainsString($expectedOutput, ob_get_contents());

        ob_clean();

        $expectedOutput = '<script src="http://mydomain.com/assets/js/jquery.js" ></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/bootstrap.js" ></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/bootstrap-datepicker.min.js" ></script>' . PHP_EOL .
            '<script src="http://mydomain.com/assets/js/custom.js" async defer></script>' . PHP_EOL;
        '<script src="https://code.jquery.com/jquery-3.2.1.min.js" ></script>' . PHP_EOL;


        $this->assetManager->dump(AssetManager::STORES['js']);

        $this->assertStringContainsString($expectedOutput, ob_get_contents());

        ob_get_clean();
    }

    public function testAssetGetTag()
    {
        $this->assetManager->registerAsset(new Asset(Asset::JS, 'js/jquery.js', 'jQuery'));

        $this->assertEquals(
            '<script src="http://mydomain.com/assets/js/jquery.js" ></script>' . PHP_EOL,
            $this->assetManager->get('jQuery')->tag());
    }

    public function testAssetUrl()
    {
        $this->assertEquals(
            'http://mydomain.com/assets/icons/person.png',
            $this->assetManager->url('icons/person.png'));

        $this->assertEquals(
            'http://mydomain.com/assets/fonts/arial.ttf',
            $this->assetManager->url('fonts/arial.ttf'));

        $this->assertEquals(
            'https://code.jquery.com/jquery-3.2.1.min.js',
            $this->assetManager->url('https://code.jquery.com/jquery-3.2.1.min.js'));
    }

}
