<?php

namespace Quantum\Tests\Libraries\Storage;

use Quantum\Libraries\Storage\Adapters\Dropbox\TokenServiceInterface;
use Mockery;

trait TokenServiceTestCase
{
    protected function mockTokenService()
    {
        $tokenServiceMock = Mockery::mock(TokenServiceInterface::class);

        $tokenServiceMock->shouldReceive('getRefreshToken')->andReturnUsing(function () {
            $this->currentResponse = (object)$this->tokensGrantResponse;
            return 'ref_tok_1234';
        });

        $tokenServiceMock->shouldReceive('getAccessToken')->andReturn('acc_tok_1234');

        $tokenServiceMock->shouldReceive('saveTokens')->andReturnUsing(function ($tokens) {
            $this->currentResponse = (object)$this->profileDataResponse;
            return true;
        });

        return $tokenServiceMock;
    }
}