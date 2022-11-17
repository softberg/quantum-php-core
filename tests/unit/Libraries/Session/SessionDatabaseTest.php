<?php

namespace Quantum\Tests\Libraries\Session;

use Quantum\Libraries\Session\Handlers\DatabaseHandler;
use Quantum\Libraries\Database\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Database;
use Quantum\Libraries\Session\Session;
use Quantum\Tests\AppTestCase;

class SessionDatabaseTest extends AppTestCase
{
    private $session;

    private $storage = [];

    public function setUp(): void
    {
        parent::setUp();

        putenv('APP_KEY=' . uniqid());

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createSessionsTable();

        $sessionModel = Database::getInstance()->getOrm('sessions');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_save_handler(new DatabaseHandler($sessionModel), true);
            session_start();
        }

        $this->session = Session::getInstance($this->storage);
    }

    public function tearDown(): void
    {
        session_write_close();
        Database::getInstance()->getOrm('sessions')->deleteMany();
    }

    public function testDatabaseSessionAll()
    {
        $this->assertEmpty($this->session->all());

        $this->session->set('test', 'Test data');

        $this->session->set('user', ['username' => 'test@unit.com']);

        $this->assertNotEmpty($this->session->all());

        $this->assertIsArray($this->session->all());

        $this->assertArrayHasKey('test', $this->session->all());
    }

    public function testDatabaseSessionGetSetHasDelete()
    {
        $this->assertNull($this->session->get('auth'));

        $this->assertFalse($this->session->has('auth'));

        $this->session->set('auth', 'Authenticated');

        $this->assertTrue($this->session->has('auth'));

        $this->assertEquals('Authenticated', $this->session->get('auth'));

        $this->session->delete('auth');

        $this->assertFalse($this->session->has('auth'));

        $this->assertNull($this->session->get('auth'));
    }

    public function testDatabaseGetSetFlash()
    {
        $this->session->setFlash('message', 'Flash message');

        $this->assertEquals('Flash message', $this->session->getFlash('message'));

        $this->assertNull($this->session->getFlash('message'));
    }


    public function testDatabaseSessionFlush()
    {
        $this->session->set('test', 'Test data');

        $this->assertNotEmpty($this->session->all());

        $this->session->flush();

        $this->assertEmpty($this->session->all());
    }

    public function testGetSessionId()
    {
        $this->assertEquals(session_id(), $this->session->getId());
    }

    public function testRegenerateSessionId()
    {
        $sessionId = $this->session->getId();

        $this->assertEquals(session_id(), $sessionId);

        $this->session->regenerateId();

        $this->assertNotEquals(session_id(), $sessionId);
    }

    public function testDatabaseSessionSubsequentRequests()
    {
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        $this->assertEmpty($this->session->all());

        $this->session->set('persists', 'Data saved in persistent storage');

        $this->assertNotEmpty($this->session->all());

        $this->assertIsArray($this->session->all());

        $this->assertEquals('Data saved in persistent storage', $this->session->get('persists'));

        session_write_close();
        unset($this->session);

        $this->assertEquals(PHP_SESSION_NONE, session_status());

        @session_start();

        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());

        $this->session = Session::getInstance($this->storage);

        $this->assertNotEmpty($this->session->all());

        $this->assertIsArray($this->session->all());

        $this->assertEquals('Data saved in persistent storage', $this->session->get('persists'));
    }

    private function _createSessionsTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS sessions (
                        id INTEGER PRIMARY KEY,
                        session_id VARCHAR(255) UNIQUE,
                        data VARCHAR(255),
                        ttl INTEGER(11)
                    )");

    }
}
