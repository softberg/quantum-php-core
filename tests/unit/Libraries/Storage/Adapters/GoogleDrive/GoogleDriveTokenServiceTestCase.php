<?php

namespace Quantum\Tests\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Contracts\TokenServiceInterface;
use Mockery;

trait GoogleDriveTokenServiceTestCase
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
            $this->currentResponse = (object)$this->fileMetadataResponse;
            return true;
        });

        return $tokenServiceMock;
    }
}