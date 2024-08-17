<?php

namespace Quantum\Tests\Libraries\Asset;

use Quantum\Libraries\Asset\Asset;
use Quantum\Tests\AppTestCase;

class AssetTest extends AppTestCase
{

    private $asset;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('base_url', 'http://mydomain.com');

        $this->asset = new Asset(Asset::JS, 'scripts/jquery.js', 'jQuery');

    }

    public function testAssetType()
    {
        $this->assertEquals(Asset::JS, $this->asset->getType());
    }

    public function testAssetPath()
    {
        $this->assertEquals('scripts/jquery.js', $this->asset->getPath());
    }

    public function testAssetName()
    {
        $this->assertEquals('jQuery', $this->asset->getName());
    }

    public function testAssetPosition()
    {
        $this->assertEquals(-1, $this->asset->getPosition());
    }

    public function testAssetAttributes()
    {
        $this->assertIsArray($this->asset->getAttributes());

        $this->assertCount(0, $this->asset->getAttributes());
    }

    public function testAssetUrl()
    {
        $this->assertEquals(
            'http://mydomain.com/assets/scripts/jquery.js',
            $this->asset->url());
    }

    public function testAssetTag()
    {
        $this->assertEquals(
            '<script src="http://mydomain.com/assets/scripts/jquery.js" ></script>' . PHP_EOL,
            $this->asset->tag());
    }

}