<?php

namespace Quantum\Tests\Libraries\Curl;

use Quantum\Exceptions\HttpException;
use Quantum\Libraries\Curl\HttpClient;
use Quantum\Tests\AppTestCase;


class HttpClientTest extends AppTestCase
{

    private $httpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new HttpClient();
    }

    public function testHttpClientGetSetMethod()
    {
        $this->assertEquals('GET', $this->httpClient->getMethod());

        $this->httpClient->setMethod('POST');

        $this->assertEquals('POST', $this->httpClient->getMethod());

        $this->expectException(HttpException::class);

        $this->expectExceptionMessage('exception.method_not_available');

        $this->httpClient->setMethod('NOPE');
    }

    public function testHttpClientGetSetData()
    {
        $this->assertNull($this->httpClient->getData());

        $this->httpClient->setData(['some key' => 'some value']);

        $this->assertNotNull($this->httpClient->getData());

        $this->assertIsArray(($this->httpClient->getData()));
    }

    public function testHttpClientIsMultiRequest()
    {
        $this->httpClient->createRequest('https://httpbin.org');

        $this->assertFalse($this->httpClient->isMultiRequest());

        $this->httpClient->createMultiRequest();

        $this->assertTrue($this->httpClient->isMultiRequest());

        $this->httpClient->createAsyncMultiRequest(function () {}, function () {});

        $this->assertTrue($this->httpClient->isMultiRequest());
    }

    public function testHttpClientCreateRequestFlow()
    {
        $this->httpClient
            ->createRequest('https://www.google.com')
            ->start();

        $this->assertIsArray($this->httpClient->getRequestHeaders());

        $this->assertIsArray($this->httpClient->getResponseHeaders());

        $this->assertIsArray($this->httpClient->getResponseCookies());
    }

    public function testHttpClientCreateMultiRequestFlow()
    {
        $this->httpClient
            ->createMultiRequest()
            ->addGet('https://httpbin.org/get')
            ->addPost('https://httpbin.org/post')
            ->start();

        $multiResponse = $this->httpClient->getResponse();

        $this->assertCount(2, $multiResponse);

        $this->assertArrayHasKey('headers', $multiResponse[0]);

        $this->assertArrayHasKey('cookies', $multiResponse[0]);

        $this->assertArrayHasKey('body', $multiResponse[0]);
    }

    public function testHttpClientCreateAsyncMultiRequestFlow()
    {
        $this->httpClient
            ->createAsyncMultiRequest(
                function ($instance) {
                    $this->assertFalse($instance->isError());

                    $this->assertEquals('https://httpbin.org/get', $instance->getUrl());
                },
                function ($instance) {
                    $this->assertTrue($instance->isError());

                    $this->assertEquals(404, $instance->getErrorCode());
                }
            )
            ->addGet('https://httpbin.org/get')
            ->addPost('https://httpbin.org/nope')
            ->start();
    }

    public function testHttpClientGetRequestHeaders()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org')
            ->setHeaders([
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
            ])
            ->start();

        $this->assertIsArray($this->httpClient->getRequestHeaders());

        $this->assertEquals('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', $this->httpClient->getRequestHeaders('accept'));

        $this->assertEquals('ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3', $this->httpClient->getRequestHeaders('accept-language'));

        $this->assertEquals('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', $this->httpClient->getRequestHeaders('user-agent'));

        $this->assertNull($this->httpClient->getRequestHeaders('custom-header'));
    }

    public function testHttpClientGetResponseHeaders()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/get')
            ->start();

        $this->assertIsArray($this->httpClient->getResponseHeaders());

        $this->assertEquals('application/json', $this->httpClient->getResponseHeaders('content-type'));

        $this->assertNull($this->httpClient->getResponseHeaders('custom-header'));
    }

    public function testHttpClientGetResponseCookies()
    {
        $this->httpClient
            ->createRequest('https://www.youtube.com/')
            ->start();

        $responseCookies = $this->httpClient->getResponseCookies();

        $this->assertIsArray($responseCookies);

        $this->assertArrayHasKey('VISITOR_INFO1_LIVE', $responseCookies);

        $this->assertIsString($this->httpClient->getResponseCookies('VISITOR_INFO1_LIVE'));

    }

    public function testHttpClientGetTextResponseBody()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org')
            ->start();

        $this->assertNotNull($this->httpClient->getResponseBody());

        $this->assertIsString($this->httpClient->getResponseBody());
    }

    public function testHttpClientGetObjectResponseBody()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/get')
            ->start();

        $responseBody = $this->httpClient->getResponseBody();

        $this->assertNotNull($responseBody);

        $this->assertIsObject($responseBody);

        $this->assertEquals('https://httpbin.org/get', $responseBody->url);
    }

    public function testHttpClientSendPostRequestAndGetResponseBody()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/post')
            ->setMethod('POST')
            ->start();

        $responseBody = $this->httpClient->getResponseBody();

        $this->assertNotNull($responseBody);

        $this->assertIsObject($responseBody);

        $this->assertEquals('https://httpbin.org/post', $responseBody->url);
    }

    public function testHttpClientSendPostRequestWithDataAndGetResponseBody()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/post')
            ->setMethod('POST')
            ->setData(['custom' => 'Custom value'])
            ->start();

        $responseBody = $this->httpClient->getResponseBody();

        $this->assertNotNull($responseBody);

        $this->assertIsObject($responseBody);

        $this->assertEquals('Custom value', $responseBody->form->custom);
    }

    public function testHttpClientGetError()
    {
        $this->httpClient
            ->createRequest('https://test.comx')
            ->start();

        $errors = $this->httpClient->getErrors();

        $this->assertIsArray($errors);

        $this->assertEquals(6, $errors['code']);

        $this->assertEquals('Couldn\'t resolve host name (CURLE_COULDNT_RESOLVE_HOST): Could not resolve host: test.comx', $errors['message']);
    }

    public function testHttpClientMultiRequestGetError()
    {
        $this->httpClient
            ->createMultiRequest()
            ->addPut('https://test.comx/put')
            ->addPatch('https://test.comx/patch')
            ->start();

        $errors = $this->httpClient->getErrors();

        $this->assertIsArray($errors);

        $this->assertCount(2, $errors);

        $this->assertEquals(6, $errors[0]['code']);

        $this->assertEquals('Couldn\'t resolve host name (CURLE_COULDNT_RESOLVE_HOST): Could not resolve host: test.comx', $errors[0]['message']);
    }

    public function testHttpClientCurlInfo()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/get')
            ->start();

        $this->assertIsArray($this->httpClient->info());

        $this->assertEquals(200, $this->httpClient->info(CURLINFO_HTTP_CODE));

        $this->assertEquals('https://httpbin.org/get', $this->httpClient->info(CURLINFO_EFFECTIVE_URL));
    }

    public function testHttpClientUrl()
    {
        $this->httpClient
            ->createRequest('https://httpbin.org/get')
            ->start();

        $this->assertIsString($this->httpClient->url());


        $this->assertEquals('https://httpbin.org/get', $this->httpClient->url());
    }

}
