<?php

namespace Quantum\Tests\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Tests\Libraries\Storage\HttpClientTestCase;
use Quantum\Tests\AppTestCase;

class GoogleDriveAppTest extends AppTestCase
{

    use GoogleDriveTokenServiceTestCase;
    use HttpClientTestCase;

    private $googleDriveApp;

    private $appKey = 'x0hwm8wy63rrynm';

    private $appSecret = 'xxx123yyy';

    private $authCode = 'd4k29ovC7-UAAAAAAAA3Q45go2mLgjMhJSeJNBOo-EA';

    private $redirectUrl = 'http://localhost/confirm';

    private $tokensGrantResponse = [
        "access_token" => "sl.BYEQ1_VadTz6nBU36WPBBVwokc3zWVMXGjcOKxV4Tadms8ZlEPM85aHVFa_k1sfjilCWOnl79RUncPZzJ3GhrqhLGIBFFRCH0rKMa_ZtcqkerJn-f5lu10Ki5PSw4fxYM80V4PL_",
        "refresh_token" => "-3S067m3M5kAAAAAAAAAAcQF8zVqFUuhK-PFkFqiOfFTgiazWj5NyU-1EGWIh0ZS"
    ];

    private $fileMetadataResponse = [
        "id" => "file1",
        "kind" => GoogleDriveApp::DRIVE_FILE_KIND,
        "name" => "myFile",
        "mimeType" => "text/plain"
    ];

    private $fileContentResponse = 'Some plain text!';
    private $errorResponse = [
        'code' => GoogleDriveApp::INVALID_TOKEN_ERROR_CODE,
        'message' => 'Invalid access token',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $tokenServiceMock = $this->mockTokenService();

        $httpClientMock = $this->mockHttpClient();

        $this->googleDriveApp = new GoogleDriveApp($this->appKey, $this->appSecret, $tokenServiceMock, $httpClientMock);
    }

    public function testGoogleDriveGetAuthUrl()
    {
        $authUrl = $this->googleDriveApp->getAuthUrl($this->redirectUrl);

        $this->assertIsString($authUrl);

        $this->assertStringContainsString('client_id', $authUrl);

        $this->assertStringContainsString('access_type', $authUrl);
    }

    public function testGoogleDriveFetchTokens()
    {
        $this->currentResponse = (object)$this->tokensGrantResponse;

        $response = $this->googleDriveApp->fetchTokens($this->authCode, $this->redirectUrl);

        $this->assertIsObject($response);

        $this->assertTrue(property_exists($response, 'access_token'));

        $this->assertTrue(property_exists($response, 'refresh_token'));
    }

    public function testGoogleDriveRpcRequest()
    {
        $this->currentResponse = (object)$this->fileMetadataResponse;

        $response = $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $this->fileMetadataResponse['id']);

        $this->assertIsObject($response);

        $this->assertTrue(property_exists($response, 'id'));

        $this->assertTrue(property_exists($response, 'kind'));

        $this->assertTrue(property_exists($response, 'name'));

        $this->assertTrue(property_exists($response, 'mimeType'));
    }

    public function testGoogleDriveGetFileInfo()
    {
        $this->currentResponse = $this->fileContentResponse;

        $response = $this->googleDriveApp->getFileInfo($this->fileMetadataResponse['id'], true);

        $this->assertIsString($response);

        $this->assertEquals('Some plain text!', $response);
    }

    public function testGoogleDriveRequestWithAccessTokenExpired()
    {
        $this->currentErrors = ["code" => 401];

        $this->currentResponse = (object)$this->errorResponse;

        $response = $this->googleDriveApp->sendRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $this->fileMetadataResponse['id']);

        $this->assertIsObject($response);

        $this->assertEquals((object)$this->fileMetadataResponse, $response);
    }

}
