<?php

namespace Quantum\Tests\Unit\Libraries\Storage\Adapters\Dropbox;

use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Tests\Unit\Libraries\Storage\HttpClientTestCase;
use Quantum\Tests\Unit\AppTestCase;

class DropboxAppTest extends AppTestCase
{
    use DropboxTokenServiceTestCase;
    use HttpClientTestCase;

    /**
     * @var DropboxApp
     */
    private $dropboxApp;

    /**
     * @var string
     */
    private $appKey = 'x0hwm8wy63rrynm';

    /**
     * @var string
     */
    private $appSecret = 'xxx123yyy';

    /**
     * @var string
     */
    private $authCode = 'd4k29ovC7-UAAAAAAAA3Q45go2mLgjMhJSeJNBOo-EA';

    /**
     * @var string
     */
    private $redirectUrl = 'http://localhost/confirm';

    /**
     * @var array
     */
    private $tokensGrantResponse = [
        'access_token' => 'sl.BYEQ1_VadTz6nBU36WPBBVwokc3zWVMXGjcOKxV4Tadms8ZlEPM85aHVFa_k1sfjilCWOnl79RUncPZzJ3GhrqhLGIBFFRCH0rKMa_ZtcqkerJn-f5lu10Ki5PSw4fxYM80V4PL_',
        'token_type' => 'bearer',
        'expires_in' => 14400,
        'refresh_token' => '-3S067m3M5kAAAAAAAAAAcQF8zVqFUuhK-PFkFqiOfFTgiazWj5NyU-1EGWIh0ZS',
        'scope' => 'account_info.read files.content.read files.content.write files.metadata.read files.metadata.write sharing.read sharing.write',
        'uid' => '64129261',
        'account_id' => 'dbid:AAC9tDKbzTQlyNms0ZcB_iH3wLv7yNn-iyE',
    ];

    /**
     * @var array
     */
    private $profileDataResponse = [
        'account_id' => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc',
        'disabled' => false,
        'email' => 'franz@dropbox.com',
        'email_verified' => true,
        'is_teammate' => false,
        'name' => [
            'abbreviated_name' => 'FF',
            'display_name' => 'Franz Ferdinand (Personal)',
            'familiar_name' => 'Franz',
            'given_name' => 'Franz',
            'surname' => 'Ferdinand',
        ],
        'profile_photo_url' => 'https://dl-web.dropbox.com/account_photo/get/69330102&size=128x128',
    ];

    /**
     * @var array
     */
    private $errorResponse = [
        'error' => [
            '.tag' => 'no_account',
        ],
        'error_summary' => 'no_account/...',
    ];

    /**
     * @var string
     */
    private $fileContentResponse = 'Some plain text!';

    public function setUp(): void
    {
        parent::setUp();

        $tokenServiceMock = $this->mockTokenService();

        $httpClientMock = $this->mockHttpClient();

        $this->dropboxApp = new DropboxApp($this->appKey, $this->appSecret, $tokenServiceMock, $httpClientMock);
    }

    public function testDropboxGetAuthUrl()
    {
        $authUrl = $this->dropboxApp->getAuthUrl($this->redirectUrl);

        $this->assertIsString($authUrl);

        $this->assertStringContainsString('client_id', $authUrl);

        $this->assertStringContainsString('token_access_type', $authUrl);
    }

    public function testDropboxFetchTokens()
    {
        $this->currentResponse = (object)$this->tokensGrantResponse;

        $response = $this->dropboxApp->fetchTokens($this->authCode, $this->redirectUrl);

        $this->assertIsObject($response);

        $this->assertTrue(property_exists($response, 'access_token'));

        $this->assertTrue(property_exists($response, 'refresh_token'));
    }

    public function testDropboxRpcRequest()
    {
        $this->currentResponse = (object)$this->profileDataResponse;

        $response = $this->dropboxApp->rpcRequest('/users/get_account');

        $this->assertIsObject($response);

        $this->assertTrue(property_exists($response, 'email'));

        $this->assertTrue(property_exists($response, 'name'));
    }

    public function testDropboxContentRequest()
    {
        $this->currentResponse = $this->fileContentResponse;

        $response = $this->dropboxApp->contentRequest('files/download', $this->dropboxApp->path('message.txt'));

        $this->assertIsString($response);

        $this->assertEquals('Some plain text!', $response);
    }

    public function testDropboxSendRequest()
    {
        $this->currentResponse = (object)$this->profileDataResponse;

        $response = $this->dropboxApp->sendRequest('https://api.dropboxapi.com/2/users/get_account');

        $this->assertIsObject($response);

        $this->assertEquals((object)$this->profileDataResponse, $response);
    }

    public function testDropboxRequestWithAccessTokenExpired()
    {
        $this->currentErrors = ['code' => 401];

        $this->currentResponse = (object)$this->errorResponse;

        $response = $this->dropboxApp->sendRequest('https://api.dropboxapi.com/2/users/get_account2');

        $this->assertIsObject($response);

        $this->assertEquals((object)$this->profileDataResponse, $response);
    }

    public function testDropboxPathNormalizer()
    {
        $this->assertEquals(['path' => '/message.txt'], $this->dropboxApp->path('/message.txt'));

        $this->assertEquals(['path' => '/message.txt'], $this->dropboxApp->path('message.txt'));
    }

}
