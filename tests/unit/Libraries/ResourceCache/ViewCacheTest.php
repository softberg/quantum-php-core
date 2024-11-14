<?php

namespace Quantum\Tests\Libraries\ResourceCache;

use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Tests\AppTestCase;
use Quantum\Loader\Setup;

class ViewCacheTest extends AppTestCase
{
	private $viewCache;
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
		$this->viewCache->set('/current-route', 'test html content', 'session-id-123', 100);

		$viewCache = $this->viewCache->get('/current-route', 'session-id-123');

		$this->assertIsString($viewCache);
		$this->assertEquals('test html content', $viewCache);
	}

	public function testGetViewCache()
	{
		$this->viewCache->set('/current-route', 'test html content', 'session-id-123', 100);
		$viewCache = $this->viewCache->get('/current-route', 'session-id-123');

		$this->assertIsString($viewCache);
		$this->assertEquals('test html content', $viewCache);
	}

	public function testExistsViewCache()
	{
		$this->viewCache->set('/current-route', 'test html content', 'session-id-123', 100);

		$this->assertIsBool($this->viewCache->exists('/current-route', 'session-id-123'));
		$this->assertTrue($this->viewCache->exists('/current-route', 'session-id-123'));
	}

	public function testDeleteViewCache()
	{
		$this->viewCache->set('/current-route', 'test html content', 'session-id-123', 100);

		$this->assertIsBool($this->viewCache->exists('/current-route', 'session-id-123'));
		$this->assertTrue($this->viewCache->exists('/current-route', 'session-id-123'));

		$this->viewCache->delete('/current-route', 'session-id-123');

		$this->assertIsBool($this->viewCache->exists('/current-route', 'session-id-123'));
		$this->assertFalse($this->viewCache->exists('/current-route', 'session-id-123'));
	}

	public function tearDown(): void
	{
		$this->viewCache->delete('/current-route', 'session-id-123');
	}
}