<?php

namespace Quantum\Tests\Helpers;

use Quantum\Libraries\Asset\Asset;
use Quantum\Tests\AppTestCase;

class AssetHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAssetUrl()
    {
        config()->set('base_url', 'http://mydomain.com');

        $this->assertEquals('http://mydomain.com/assets/css/style.css', asset()->url('css/style.css'));

        $this->assertSame('http://mydomain.com/assets/js/script.js', asset()->url('js/script.js'));
    }

    public function testPublishedAssets()
    {
        config()->set('base_url', 'http://mydomain.com');

        asset()->register([
            new Asset(Asset::CSS, 'css/style.css'),
            new Asset(Asset::CSS, 'css/responsive.css')
        ]);

        asset()->register([
            new Asset(Asset::JS, 'js/bootstrap.js'),
            new Asset(Asset::JS, 'js/bootstrap-datepicker.min.js'),
            new Asset(Asset::JS, 'js/jQuery.js', 'jQuery', 0)
        ]);

        $expectedOutput = '<link rel="stylesheet" type="text/css" href="' . asset()->url('css/style.css') . '">' . PHP_EOL .
            '<link rel="stylesheet" type="text/css" href="' . asset()->url('css/responsive.css') . '">' . PHP_EOL .
            '<script src="' . asset()->url('js/jQuery.js') . '" ></script>' . PHP_EOL .
            '<script src="' . asset()->url('js/bootstrap.js') . '" ></script>' . PHP_EOL .
            '<script src="' . asset()->url('js/bootstrap-datepicker.min.js') . '" ></script>' . PHP_EOL;

        ob_start();

        assets('css');
        assets('js');

        $this->assertStringContainsString($expectedOutput, ob_get_contents());

        ob_get_clean();
    }
}