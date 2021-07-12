<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Curl\Curl;

class CurlTest extends TestCase
{

    private $curl;
    private $multiCurl;

    public function setUp(): void
    {
        $this->curl = new Curl();
        $this->multiCurl = new Curl('multi');
    }

    public function testCurlConstructor()
    {
        $this->assertInstanceOf('Quantum\Libraries\Curl\Curl', $this->curl);

        $this->assertInstanceOf('Quantum\Libraries\Curl\Curl', $this->multiCurl);
    }

    public function testSetGetOptions()
    {
        $this->curl->setOptions([
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_NOBODY => true,
            CURLOPT_HEADER => 1
        ]);

        $this->assertEquals('GET', $this->curl->getOption(CURLOPT_CUSTOMREQUEST));

        $this->assertTrue($this->curl->getOption(CURLOPT_NOBODY));

        $this->assertEquals(1, $this->curl->getOption(CURLOPT_HEADER));

        $this->assertNull($this->curl->getOption(CURLOPT_VERBOSE));
    }

    public function testSetGetRequestHeaders()
    {
        $this->curl->setRequestHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
        ]);

        $this->curl->run('https://httpbin.org');

        $this->assertIsObject($this->curl->getRequestHeaders());

        $this->assertEquals('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', $this->curl->getRequestHeaders('Accept'));

        $this->assertEquals('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', $this->curl->getRequestHeaders('User-Agent'));

        $this->assertNull($this->curl->getRequestHeaders('custom-header'));
    }

    public function testGetResponseHeaders()
    {
        $this->curl->run('https://httpbin.org/get');

        $this->assertIsArray($this->curl->getResponseHeaders());
        
        $this->assertEquals('application/json', $this->curl->getResponseHeaders('content-type'));

        $this->assertNull($this->curl->getResponseHeaders('custom-header'));
    }

    public function testGetRresponseBody()
    {
        $this->curl->run('https://httpbin.org');

        $this->assertNotNull($this->curl->getResponseBody());

        $this->assertIsString($this->curl->getResponseBody());
    }

    public function testCurlInfo()
    {
        $this->curl->run('https://httpbin.org/');

        $this->assertIsArray($this->curl->info());

        $this->assertEquals(200, $this->curl->info(CURLINFO_HTTP_CODE));

        $this->assertEquals('https://httpbin.org/', $this->curl->info(CURLINFO_EFFECTIVE_URL));

        $this->assertFalse($this->curl->info(CURLOPT_PRIVATE));
    }

    public function testGetError()
    {
        $this->curl->run('https://test.comx');

        $this->assertIsArray($this->curl->getErrors());

        $this->assertEquals(6, current($this->curl->getErrors())['code']);

        $this->assertEquals('Couldn\'t resolve host name: Could not resolve host: test.comx', current($this->curl->getErrors())['message']);
    }

    public function testMultiCurl()
    {
        $curl = (new Curl('multi'))
                ->addGet('https://httpbin.org/anything')
                ->addGet('https://httpbin.org/get')
                ->addPost('https://httpbin.org/delay/2')
                ->run();

        $responseBody = $curl->getResponseBody();

        $this->assertIsArray($responseBody);

        $this->assertCount(3, $responseBody);

        $this->assertIsObject($responseBody[0]);
    }

}
