<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.7
 */

namespace Quantum\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Contracts\TokenServiceInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Storage\Contracts\CloudAppInterface;
use Quantum\Libraries\Storage\Traits\CloudAppTrait;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\App\Exceptions\BaseException;
use Exception;

/**
 * Class GoogleDriveApp
 * @package Quantum\Libraries\Storage
 */
class GoogleDriveApp implements CloudAppInterface
{

    use CloudAppTrait;

    /**
     * Authorization URL
     */
    const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * Authorization scope
     */
    const AUTH_SCOPE = 'https://www.googleapis.com/auth/drive';

    /**
     * Token URL
     */
    const AUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /**
     * URL for file metadata operations
     */
    const FILE_METADATA_URL = 'https://www.googleapis.com/drive/v3/files';

    /**
     * URL for file media operations
     */
    const FILE_MEDIA_URL = 'https://www.googleapis.com/upload/drive/v3/files';

    /**
     * Folder mimetype
     */
    const FOLDER_MIMETYPE = 'application/vnd.google-apps.folder';

    /**
     * Kind/Type  of drive file
     */
    const DRIVE_FILE_KIND = 'drive#file';

    /**
     * Error code for invalid token
     */
    const INVALID_TOKEN_ERROR_CODE = 401;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $appKey;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var TokenServiceInterface
     */
    private $tokenService;

    /**
     * GoogleDriveApp constructor
     * @param string $appKey
     * @param string $appSecret
     * @param TokenServiceInterface $tokenService
     * @param HttpClient $httpClient
     */
    public function __construct(string $appKey, string $appSecret, TokenServiceInterface $tokenService, HttpClient $httpClient)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->tokenService = $tokenService;
        $this->httpClient = $httpClient;
    }

    /**
     * Gets Auth URL
     * @param string $redirectUrl
     * @param string $accessType
     * @return string
     * @throws BaseException
     * @throws CryptorException
     * @throws DatabaseException
     */
    public function getAuthUrl(string $redirectUrl, string $accessType = "offline"): string
    {
        $params = [
            'client_id' => $this->appKey,
            'response_type' => 'code',
            'state' => csrf_token(),
            'scope' => self::AUTH_SCOPE,
            'redirect_uri' => $redirectUrl,
            'access_type' => $accessType,
        ];

        return self::AUTH_URL . '?' . http_build_query($params, '', '&');
    }

    /**
     * Fetch tokens
     * @param string $code
     * @param string $redirectUrl
     * @return object|null
     * @throws Exception
     */
    public function fetchTokens(string $code, string $redirectUrl = ''): ?object
    {
        $params = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'client_id' => $this->appKey,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $redirectUrl
        ];

        $response = $this->sendRequest(self::AUTH_TOKEN_URL, $params);

        $this->tokenService->saveTokens($response->access_token, $response->refresh_token);

        return $response;
    }

    /**
     * Fetches the access token by refresh token
     * @param string $refreshToken
     * @return string
     * @throws Exception
     */
    private function fetchAccessTokenByRefreshToken(string $refreshToken): string
    {
        $params = [
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'client_id' => $this->appKey,
            'client_secret' => $this->appSecret
        ];

        $response = $this->sendRequest(self::AUTH_TOKEN_URL, $params);

        $this->tokenService->saveTokens($response->access_token);

        return $response->access_token;
    }

    /**
     * Sends rpc request
     * @param string $url
     * @param mixed $params
     * @param string $method
     * @param string $contentType
     * @return mixed|null
     * @throws Exception
     */
    public function rpcRequest(string $url, $params = [], string $method = 'POST', string $contentType = 'application/json')
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->tokenService->getAccessToken(),
                'Content-Type' => $contentType
            ];
            return $this->sendRequest($url, $params, $headers, $method);
        }catch (Exception $e){
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Gets file information
     * @param string $fileId
     * @param bool $media
     * @param array $params
     * @return mixed|null
     * @throws Exception
     */
    public function getFileInfo(string $fileId, bool $media = false, array $params = []){
        $queryParam = $media ? '?alt=media' : '?fields=*';
        return $this->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $fileId . $queryParam, $params, 'GET');
    }

    /**
     * Checks if the access token need refresh
     * @param int $code
     * @param object|null $message
     * @return bool
     */
    private function accessTokenNeedsRefresh(int $code, ?object $message = null): bool
    {
        if ($code != self::INVALID_TOKEN_ERROR_CODE) {
            return false;
        }

        return true;
    }
}