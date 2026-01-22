<?php

namespace Quantum\Tests\Unit\Libraries\Storage;

use Quantum\Libraries\HttpClient\HttpClient;
use Mockery;

trait HttpClientTestCase
{
    protected $url;

    protected $response;

    protected $currentResponse;

    protected $currentErrors;

    protected function mockHttpClient()
    {
        $httpClientMock = Mockery::mock(HttpClient::class);

        $httpClientMock->shouldReceive('createRequest')->andReturnUsing(function ($url) use ($httpClientMock) {
            $this->url = $url;
            return $httpClientMock;
        });

        $httpClientMock->shouldReceive('setMethod')->andReturnSelf();

        $httpClientMock->shouldReceive('setHeaders')->andReturnSelf();

        $httpClientMock->shouldReceive('getRequestHeaders')->andReturn([]);

        $httpClientMock->shouldReceive('getData')->andReturn([]);

        $httpClientMock->shouldReceive('setData')->andReturn($httpClientMock);

        $httpClientMock->shouldReceive('start')->andReturnUsing(function () use ($httpClientMock) {
            $this->response[$this->url]['body'] = $this->currentResponse;
            $this->response[$this->url]['errors'] = $this->currentErrors;
            $this->currentResponse = [];
            $this->currentErrors = [];
            return $httpClientMock;
        });

        $httpClientMock->shouldReceive('getErrors')->andReturnUsing(fn () => (array) $this->response[$this->url]['errors']);

        $httpClientMock->shouldReceive('getResponseBody')->andReturnUsing(fn () => $this->response[$this->url]['body']);

        $httpClientMock->shouldReceive('url')->andReturnUsing(fn () => $this->url);

        return $httpClientMock;
    }
}
