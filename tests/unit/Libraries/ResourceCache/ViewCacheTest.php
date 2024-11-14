<?php

namespace Quantum\Tests\Libraries\ResourceCache;

use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Tests\AppTestCase;
use Quantum\Loader\Setup;

class ViewCacheTest extends AppTestCase
{
	private $viewCache;

	private $route = '/current-route';

	private $sessionId = 'session-id-123';

	private $content = 'test html content';

	private $ttl = 100;

	public function setUp():void
	{
		parent::setUp();
		if (!config()->has('view_cache')){
			config()->import(new Setup('config', 'view_cache'));
		}
		$this->viewCache = new ViewCache();
	}

	public function testStoreViewCache()
	{
		$this->viewCache->set($this->route, $this->content, $this->sessionId, $this->ttl);

		$viewCache = $this->viewCache->get($this->route, $this->sessionId);

		$this->assertIsString($viewCache);
		$this->assertEquals($this->content, $viewCache);
	}

	public function testGetViewCache()
	{
		$this->viewCache->set($this->route, $this->content, $this->sessionId, $this->ttl);
		$viewCache = $this->viewCache->get($this->route, $this->sessionId);

		$this->assertIsString($viewCache);
		$this->assertEquals($this->content, $viewCache);
	}

	public function testExistsViewCache()
	{
		$this->viewCache->set($this->route, $this->content, $this->sessionId, $this->ttl);

		$this->assertIsBool($this->viewCache->exists($this->route, $this->sessionId));
		$this->assertTrue($this->viewCache->exists($this->route, $this->sessionId));
	}

	public function testDeleteViewCache()
	{
		$this->viewCache->set($this->route, $this->content, $this->sessionId, $this->ttl);

		$this->assertIsBool($this->viewCache->exists($this->route, $this->sessionId));
		$this->assertTrue($this->viewCache->exists($this->route, $this->sessionId));

		$this->viewCache->delete($this->route, $this->sessionId);

		$this->assertIsBool($this->viewCache->exists($this->route, $this->sessionId));
		$this->assertFalse($this->viewCache->exists($this->route, $this->sessionId));
	}

	public function tearDown(): void
	{
		$this->viewCache->delete($this->route, $this->sessionId);
	}
}