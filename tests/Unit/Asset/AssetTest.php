<?php

namespace Quantum\Tests\Unit\Asset;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Asset\Asset;

class AssetTest extends AppTestCase
{
    private Asset $asset;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.base_url', 'http://mydomain.com');

        $this->asset = new Asset(Asset::JS, 'scripts/jquery.js', 'jQuery');

    }

    public function testAssetType(): void
    {
        $this->assertEquals(Asset::JS, $this->asset->getType());
    }

    public function testAssetPath(): void
    {
        $this->assertEquals('scripts/jquery.js', $this->asset->getPath());
    }

    public function testAssetName(): void
    {
        $this->assertEquals('jQuery', $this->asset->getName());
    }

    public function testAssetPosition(): void
    {
        $this->assertEquals(-1, $this->asset->getPosition());
    }

    public function testAssetAttributes(): void
    {
        $this->assertIsArray($this->asset->getAttributes());

        $this->assertCount(0, $this->asset->getAttributes());
    }

    public function testAssetUrl(): void
    {
        $this->assertEquals(
            'http://mydomain.com/assets/scripts/jquery.js',
            $this->asset->url()
        );
    }

    public function testAssetTag(): void
    {
        $this->assertEquals(
            '<script src="http://mydomain.com/assets/scripts/jquery.js" ></script>' . PHP_EOL,
            $this->asset->tag()
        );
    }

}
