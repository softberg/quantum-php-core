<?php

namespace Quantum\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Curl\HttpClient;
use Quantum\Http\Response;
use Exception;

class GoogleDriveApp
{
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
    private $appKey = null;

    /**
     * @var string
     */
    private $appSecret = null;

    /**
     * @var TokenServiceInterface
     */
    private $tokenService = null;

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

    public function getAuthUrl($redirectUrl, $accessType = "offline"): string
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

    public function fetchTokens($code, $redirectUrl = '', $byRefresh = false): ?object
    {
        $codeKey = $byRefresh ? 'refresh_token' : 'code';

        $params = [
            $codeKey => $code,
            'grant_type' => $byRefresh ? 'refresh_token' : 'authorization_code',
            'client_id' => $this->appKey,
            'client_secret' => $this->appSecret,
        ];

        if(!$byRefresh){
            $params['redirect_uri'] = $redirectUrl;
        }

        $tokenUrl = self::AUTH_TOKEN_URL;

        $response = $this->sendRequest($tokenUrl, $params);

        $this->tokenService->saveTokens($response->access_token, !$byRefresh ? $response->refresh_token : null);

        return $response;
    }

    public function sendRequest(string $uri, $data = null, array $headers = [], $method = 'POST')
    {
        $this->httpClient
            ->createRequest($uri)
            ->setMethod($method)
            ->setData($data)
            ->setHeaders($headers)
            ->start();


        $errors = $this->httpClient->getErrors();
        $responseBody = $this->httpClient->getResponseBody();

        if ($errors) {
            $code = $errors['code'];

            if ($code == self::INVALID_TOKEN_ERROR_CODE) {
                $prevUrl = $this->httpClient->url();
                $prevData = $this->httpClient->getData();
                $prevHeaders = $this->httpClient->getRequestHeaders();

                $refreshToken = $this->tokenService->getRefreshToken();

                $response = $this->fetchTokens($refreshToken , '', true);

                $prevHeaders['Authorization'] = 'Bearer ' . $response->access_token;

                $responseBody = $this->sendRequest($prevUrl, $prevData, $prevHeaders);

            } else {
                throw new Exception(json_encode($responseBody ?? $errors), E_ERROR);
            }
        }

        return $responseBody;
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
    public function rpcRequest(string $url, $params = [], $method = 'POST', $contentType = 'application/json')
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
     * @param mixed $params
     * @return mixed|null
     * @throws Exception
     */
    public function getFileInfo(string $fileId, $media = false, $params = []){
        $queryParam = $media ? '?alt=media' : '?fields=*';
        return $this->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $fileId . $queryParam, $params, 'GET');
    }
}