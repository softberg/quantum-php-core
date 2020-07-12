<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Asset\AssetManager;

class AssetManagerTest extends TestCase
{

    private $assetManager;

    public function setUp(): void
    {
        $this->assetManager = new AssetManager();

        $reflectionClass = new \ReflectionClass(AssetManager::class);

        $reflectionPropertyCssAssetStore = $reflectionClass->getProperty('cssAssetStore');

        $reflectionPropertyCssAssetStore->setAccessible(true);

        $reflectionPropertyCssAssetStore->setValue([
            'ordered' => [],
            'unordered' => [],
        ]);
        
        $reflectionPropertyJsAssetStore = $reflectionClass->getProperty('jsAssetStore');

        $reflectionPropertyJsAssetStore->setAccessible(true);

        $reflectionPropertyJsAssetStore->setValue([
            'ordered' => [],
            'unordered' => [],
        ]);
    }

    public function testRegisterAndPublishCSS()
    {
        $this->assetManager->registerCSS([
            'fakepath/style.css',
            'fakepath/responsive.css'
        ]);

        $published = $this->assetManager->publishCSS();

        $this->assertNotEmpty($published);

        $this->assertEquals(2, count($published));

        $this->assertEquals('fakepath/style.css', $published[0]);

        $this->assertEquals('fakepath/responsive.css', $published[1]);

        $this->assetManager->registerCSS([
            ['fakepath/reset.css', 0],
            ['fakepath/media.css', 2]
        ]);

        $published = $this->assetManager->publishCSS();

        $this->assertEquals(4, count($published));

        $this->assertEquals('fakepath/reset.css', $published[0]);

        $this->assertEquals('fakepath/style.css', $published[1]);

        $this->assertEquals('fakepath/media.css', $published[2]);

        $this->assertEquals('fakepath/responsive.css', $published[3]);
    }

    public function testRegisterAndPublishJS()
    {
        $this->assetManager->registerJS([
            'fakepath/bootstrap.js',
            'fakepath/bootstrap-datepicker.min.js'
        ]);

        $published = $this->assetManager->publishJS();

        $this->assertNotEmpty($published);

        $this->assertEquals(2, count($published));

        $this->assertEquals('fakepath/bootstrap.js', $published[0]);

        $this->assertEquals('fakepath/bootstrap-datepicker.min.js', $published[1]);

        $this->assetManager->registerJS([
            ['fakepath/modernizr.js', 0],
            ['fakepath/jquery.js', 1]
        ]);

        $published = $this->assetManager->publishJS();

        $this->assertEquals(4, count($published));

        $this->assertEquals('fakepath/modernizr.js', $published[0]);

        $this->assertEquals('fakepath/jquery.js', $published[1]);

        $this->assertEquals('fakepath/bootstrap.js', $published[2]);

        $this->assertEquals('fakepath/bootstrap-datepicker.min.js', $published[3]);
    }

}
