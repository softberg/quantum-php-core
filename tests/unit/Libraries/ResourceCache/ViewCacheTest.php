<?php

namespace Quantum\Tests\Libraries\ResourceCache;

use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Tests\AppTestCase;

class ViewCacheTest extends AppTestCase
{
	private $viewCache;

	private $route = '/current-route';

	private $content = 'test html content';

	public function setUp():void
	{
		parent::setUp();

		config()->set('resource_cache', true);
		$this->viewCache = ViewCache::getInstance();
	}

	public function testStoreAndGetViewCache()
	{
		$this->viewCache->set($this->route, $this->content);

		$viewCache = $this->viewCache->get($this->route);

		$this->assertIsString($viewCache);
		$this->assertEquals($this->content, $viewCache);
	}

	public function testExistsViewCache()
	{
		$this->viewCache->set($this->route, $this->content);

		$this->assertIsBool($this->viewCache->exists($this->route));
		$this->assertTrue($this->viewCache->exists($this->route));
	}

	public function testDeleteViewCache()
	{
		$this->viewCache->set($this->route, $this->content);

		$this->assertIsBool($this->viewCache->exists($this->route));
		$this->assertTrue($this->viewCache->exists($this->route));

		$this->viewCache->delete($this->route);

		$this->assertIsBool($this->viewCache->exists($this->route));
		$this->assertFalse($this->viewCache->exists($this->route));
	}

	public function tearDown(): void
	{
		$this->viewCache->delete($this->route);
	}
}